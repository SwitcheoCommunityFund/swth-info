"use strict";

const request = require('request');
const { WalletClient, RestClient} = require('tradehub-api-js')
const abiCoder = require("web3-eth-abi");
const { Client } = require('pg')
const bech32 = require('bech32')
const colors = require('colors');
const nodemailer = require("nodemailer");

const db = new Client({
    database: 'switcheo',
    user: 'service_manager',
    password: '*****',
})

/* SWITCHEO API PARAMS */
const SWTH_NODE_ADDR = 'https://tradescan.switcheo.org';
const NETWORK = 'MAINNET'
const MNEMONICS = '';
var SWTH_WALLET, SWTH_REST;

/* ETHEREUM API PARAMS */
const ETH_API_ADDR = 'https://api.etherscan.io';
const APIKEY = '****';
const TRACK_CONTRACT = '0x9a016ce184a22dbf6c17daa59eb7d3140dbd1c54';

/* SYSTEM PARAMS */
const SWTH_CHECK_BALANCE_ATTEMPTS = 5;
const SWTH_RETRY_CHECK_BALANCE_PAUSE = 2;//sec
//const ETH_START_CHECK_BLOCK = 0;
const ETH_PAGE_PAUSE = 5;//sec
const ETH_WAIT_PAUSE = 120;//sec
const ETH_TRANSACTIONS_LIMIT = 100;

var inputFxs={};
const AIRDROP_AMOUNT = 5;
const VARS_TYPE = 'test';

/* MAILER PARAMS */
const mailer_login = 'login@gmail.com';
const mailer_name = 'SWTH.INFO';
const mailer_password = '****';
const mailer_receivers = ['admin_login@gmail.com'];

let transporter = nodemailer.createTransport({
    host: "smtp.gmail.com",
    port: 465,//587,
    secure: true, // true for 465, false for other ports
    auth: {
        user: mailer_login,
        pass: mailer_password,
    },
});

function sendErrorEmail(data){
    var time = (new Date()).toISOString().replace(/T/,' ');
    var email_text = JSON.stringify(data,jsonErrorReplacer);
    return transporter.sendMail({
        from    : `"${mailer_name}" <${mailer_login}>`,
        to      : mailer_receivers.join(", "),
        subject : "[AIRDROP ERROR] "+time,
        text    : email_text,
    });
}


function getSwitcheoWalletBalance(wallet)
{
    return new Promise((resolve,reject) => {
        var req = request.get({
                url:`${SWTH_NODE_ADDR}/get_balance?account=${wallet}`,
                timeout: 10000,
                json: true,
            },
            (err,resp,body)=>{
            try{
                if(err){ reject({type:'http_err',error:err}); }

                if(typeof body != 'object'){
                    reject({type:'swth_api_err',error:{status:'unknown',message:body}});
                } else {
                    resolve(body);
                } /*else {
                    reject({type:'swth_api_err',error:{status:'empty',message:'wallet not found', wallet:wallet}});
                }*/
            }catch(e){
                reject({type:'unknown_err',error:{response:body}});
            }
        })
    });
}

function getSwitcheoTransaction(hash)
{
    return new Promise((resolve,reject) => {
        var req = request.get({
                url:`${SWTH_NODE_ADDR}/get_transaction?hash=${hash}`,
                timeout: 10000,
                json: true,
            },
            (err,resp,body)=>{
            try{
                if(err){ reject({type:'http_err',error:err}); }

                if(typeof body != 'object'){
                    reject({type:'swth_api_err',error:{status:'unknown',message:body}});
                } else {
                    resolve(body);
                }
            }catch(e){
                reject({type:'unknown_err',error:{response:body}});
            }
        })
    });
}


function sendSwitcheoTokens(wallet,value)
{
    var message = {
        amount : [{ denom: 'swth', amount: (value*100000000)+''}],
        from_address : SWTH_WALLET.pubKeyBech32,
        to_address : wallet
    }
    return SWTH_REST.send(message);
}

function saveAirdropState(state,wallet,tx,log=null){
    return  new Promise((resolve,reject) => {
        var time = (new Date()).toISOString().replace(/T/,' ');
        db.query(`INSERT 
                  INTO airdrop_log (tx_id,air_time,amount,wallet,status,"log")
                  VALUES ($1,$2,$3,$4,(SELECT id FROM airdrop_log_states WHERE state_code = $5),$6)
        `,[tx.hash,time,AIRDROP_AMOUNT,wallet,state,log], (err, res) => {
            if(err) reject(err);
            resolve();
        })
    });
}

function pause(sec){return new Promise((resolve)=>{setTimeout(function(){resolve(true);},sec * 1000)});}

function jsonErrorReplacer(key, value)
{
    if (value instanceof Error) {
        return {name: value.name, message: value.message, stack: value.stack,}
    }
    return value
}

function getLastCheckBlock()
{
    return  new Promise((resolve,reject) => {
        db.query("SELECT * from airdrop_vars where code = $1",[VARS_TYPE], (err, res) => {
            if(err) reject(err);
            if(typeof res.rows[0] == 'undefined') reject('cant find var');
            resolve(res.rows[0].last_check_block);
        })
    });
}

function setLastCheckBlock(block)
{
    return  new Promise((resolve,reject) => {
        db.query("UPDATE airdrop_vars SET last_check_block = $1 where code = $2",[block,VARS_TYPE], (err, res) => {
            if(err) reject(err);
            resolve();
        })
    });
}

function indexAbiInputSchema(abi)
{
    var inputSchemas ={};
    for(var i in abi){
        if(abi[i].type=='function'){
            inputSchemas[abiCoder.encodeFunctionSignature(abi[i])] = abi[i];
        } else if(abi[i].type=='event') {
            inputSchemas[abiCoder.encodeEventSignature(abi[i])] = abi[i];
        }
    }
    return inputSchemas;
}

function getAbiInputSchema()
{
    return new Promise((resolve,reject) => {
        var req = request.post({
                url:`${ETH_API_ADDR}/api?module=contract&action=getabi&address=${TRACK_CONTRACT}&apikey=${APIKEY}`,
                timeout: 10000,
                json: true,
            },
            (err,resp,body)=>{
            if(err){ reject({type:'http_err',error:err}); }

            console.log(body);
            if(body.status==1) {
                resolve(indexAbiInputSchema(JSON.parse(body.result)));
            } else {
                //throw ({type:'eth_api_err',error:{status:resp.status,message:resp.message}});
                reject({type:'eth_api_err',error:{status:body.status,message:body.message}});
            }
        })
    });
}

function decodeTxInput(input)
{
    var extra = (input.length-2)%64;
    var fx = input.substr(0,(extra==0?64:extra)+2);
    var encoded = input.slice((extra==0?64:extra)+2);
    var input_schema = inputFxs[fx];
    if(input_schema==undefined) return {};
    return {
        fx_name : input_schema.name==undefined?input_schema.type:input_schema.name,
        values : abiCoder.decodeParameters(input_schema.inputs,encoded)
    };
}

function decodeSwthAddr(addr)
{
    return bech32.encode('swth', bech32.toWords(Buffer.from(addr.slice(2), 'hex')))
}

function getTransactions(startblock=0,page=1)
{
    return new Promise((resolve,reject)=>{
        var req = request.post({
                url: ETH_API_ADDR+'/api?module=account&action=txlist'
                    +'&address='+TRACK_CONTRACT
                    +'&startblock='+startblock
                    +'&sort=asc'
                    +'&offset='+ETH_TRANSACTIONS_LIMIT //limit
                    +'&page='+page
                    +'&apikey='+APIKEY,
                timeout: 10000,
                json: true
            }
            ,(err,resp,body)=>{
                try{
                    if(err){ reject({type:'http_err',error:err}); }

                    if(body.status==1) {
                        resolve(body.result);
                    } else if(body.status==0 && body.message=='No transactions found')
                    {
                        resolve({});
                    } else {
                        reject({type:'eth_api_err',error:{status:body.status,message:body.message}});
                    }
                } catch(e){
                    reject({type:'unknown_err',error:{response:body}});
                }
            }).on('error',(err)=>{reject({type:'http_err',error:err});});
    });
}

async function airdrop(wallet,tx)
{
    var attempts = SWTH_CHECK_BALANCE_ATTEMPTS;
    /* check wallet balance with retry on http error */
    while(1) {
        try {
            console.log(`CHECK BALANCE OF WALLET ${wallet}`);
            var balance = await getSwitcheoWalletBalance(wallet);
            if(balance.swth==undefined || balance.swth.available <= 1){
                console.log(`SEND ${AIRDROP_AMOUNT}swth TO ${wallet} [${tx.hash}]`.green)
                var send_tokens = await sendSwitcheoTokens(wallet,AIRDROP_AMOUNT);
                var send_tx_state = await getSwitcheoTransaction(send_tokens.txhash);
                if(send_tx_state.code==0){
                    await saveAirdropState('success',wallet,tx);
                } else {
                    console.log(`xxx SEND ${AIRDROP_AMOUNT}swth TO ${wallet} xxx`.red)
                    await saveAirdropState('airdrop_fail',wallet,tx);
                }
                //await saveAirdropState('success',wallet,tx);
            } else {
                console.log(`HIGH BALLANCE OF WALLET ${wallet}`.gray);
                await saveAirdropState('high_balance',wallet,tx);
            }
            break;
        }catch(e){
            if( (e.type=='http_err' || (e.type=='swth_api_err'&&e.error.status=='unknown')) && --attempts>0){
                console.log(`retry check balance of ${wallet} | attempts  ${attempts}`.cyan);
                await pause(SWTH_RETRY_CHECK_BALANCE_PAUSE);
            } else {
                console.log('AIRDROP ERROR'.red,e,tx.hash);
                sendErrorEmail(e);
                return saveAirdropState('check_balance_error',wallet,tx);
            }
        }
    }
}

async function trackContractTransactions()
{
    try{
        SWTH_WALLET = await WalletClient.connectMnemonic(MNEMONICS, NETWORK);
        SWTH_REST = new RestClient({ wallet:SWTH_WALLET, network:NETWORK });

        console.log('getAbiInputSchema');
        inputFxs = await getAbiInputSchema();
        console.log('connect pg');
        await db.connect();

        var page = 1;
        var last_block = await getLastCheckBlock();
        var max_block = last_block;

        if(typeof ETH_START_CHECK_BLOCK != 'undefined')
        {
            last_block = Math.max(ETH_START_CHECK_BLOCK,last_block);
        }

        console.log(`LAST CHECKED BLOCK IS : ${last_block}`.green);

        while(1)
        {
            var txs = await getTransactions(last_block, page);
            for(var i in txs){
                var txInput = decodeTxInput(txs[i].input);
                //console.log(txInput);
                if(txInput.fx_name=='createWallet' && txs[i].isError=='0'){
                    //console.log(`tx_hash : ${txs[i].hash}`);
                    var switcheo_wallet = decodeSwthAddr(txInput.values['_swthAddress']);
                    airdrop(switcheo_wallet,txs[i])
                }
                max_block = Math.max(last_block,parseInt(txs[i].blockNumber));
            }
            if(txs.length >= ETH_TRANSACTIONS_LIMIT){
                page++;
                console.log(`NEXT PAGE OF TRANSACTIONS: ${page} [last_blk:${max_block}]`.yellow);
                setLastCheckBlock(max_block);
                await pause(ETH_PAGE_PAUSE);
            } else {
                page = 0;
                last_block = max_block + 1;
                var time = (new Date()).toISOString().replace(/T/,' ');
                console.log(`WAITING OF TRANSACTIONS AFTER ${last_block} BLOCK [${time}]`.yellow);
                await pause(ETH_WAIT_PAUSE);
            }
        }
    }catch (e) {
        console.error('ERROR ON TRACKING'.red,e);
        sendErrorEmail(e);
        process.exit(1);
    }

}


trackContractTransactions();

process.on('uncaughtException', function(err){
    sendErrorEmail(err);
    process.exit(1);
});