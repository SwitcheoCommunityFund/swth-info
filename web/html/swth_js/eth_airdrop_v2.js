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
    password: '****',
})

/* SWITCHEO API PARAMS */
const SWTH_NODE_ADDR = 'https://tradescan.switcheo.org';
const NETWORK = 'MAINNET'
const MNEMONICS = '*****';
var SWTH_WALLET, SWTH_REST;

/* ETHEREUM API PARAMS */
const ETH_API_ADDR = 'https://api.etherscan.io';
const APIKEY = '*****';
const TRACK_CONTRACT = '0x9a016ce184a22dbf6c17daa59eb7d3140dbd1c54';

/* SYSTEM PARAMS */
const SWTH_API_ATTEMPTS = 5;
const SWTH_API_RETRY_PAUSE = 4;//sec
//const ETH_START_CHECK_BLOCK = 0;
const ETH_PAGE_PAUSE = 5;//sec
const ETH_WAIT_PAUSE = 120;//sec
const ETH_TRANSACTIONS_LIMIT = 100;

var inputFxs={};
const AIRDROP_AMOUNT = 5;
const VARS_TYPE = 'test_v2';

/* MAILER PARAMS */
const mailer_login = 'admin@gmail.com';
const mailer_name = 'SWTH.INFO';
const mailer_password = '*****';
const mailer_receivers = ['admin_login@gmail.com'];



function sendErrorEmail(data)
{
    var transporter = nodemailer.createTransport({
        host: "smtp.gmail.com",
        port: 465,//587,
        secure: true, // true for 465, false for other ports
        auth: {
            user: mailer_login,
            pass: mailer_password,
        },
    });

    var time = (new Date()).toISOString().replace(/T/,' ');
    var email_text = JSON.stringify(data,jsonErrorReplacer);
    return transporter.sendMail({
        from    : `"${mailer_name}" <${mailer_login}>`,
        to      : mailer_receivers.join(", "),
        subject : "[AIRDROP ERROR] "+time,
        text    : email_text
    });
}

function getSwitcheoWalletBalance(wallet,show_retries=false)
{
    return new Promise(async function (resolve,reject){
        var attempts=SWTH_API_ATTEMPTS;
        while(1){
            try{
                if(show_retries && SWTH_API_ATTEMPTS>attempts)
                    console.log(`retry check balance of ${wallet} | attempts left ${attempts}`.cyan);
                var balance = await SWTH_REST.getWalletBalance({address: wallet});
                resolve(balance);
                break;
            }catch (e) {
                if(--attempts<0){
                    reject(e);
                    break;
                } else {
                    await pause(SWTH_API_RETRY_PAUSE);
                }
            }
        }
    });
}

function getSwitcheoTransaction(hash,show_retries=false)
{
    return new Promise(async function (resolve,reject){
        var attempts=SWTH_API_ATTEMPTS;
        while(1){
            try{
                if(show_retries && SWTH_API_ATTEMPTS>attempts)
                    console.log(`retry check Switcheo Tx ${hash} | attempts left ${attempts}`.cyan);
                var tx = await SWTH_REST.getTx({id: hash});
                resolve(tx);
                break;
            }catch (e) {
                if(--attempts<0){
                    reject(e);
                    break;
                } else {
                    await pause(SWTH_API_RETRY_PAUSE);
                }
            }
        }
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

function pause(sec){return new Promise((resolve)=>{setTimeout(function(){resolve(true);},sec * 1000)});}

function jsonErrorReplacer(key, value)
{
    if (value instanceof Error) {
        return {name: value.name, message: value.message, stack: value.stack,}
    }
    return value
}

function saveAirdropState(state,wallet,tx,log=null)
{
    return  new Promise((resolve,reject) => {
        var time = (new Date()).toISOString().replace(/T/,' ');
        log = log==null?null:JSON.stringify(log,jsonErrorReplacer);
        db.query(`INSERT 
                  INTO airdrop_log (tx_id,air_time,amount,wallet,status,"log")
                  VALUES ($1,$2,$3,$4,(SELECT id FROM airdrop_log_states WHERE state_code = $5),$6)
                  ON CONFLICT DO NOTHING
        `,[tx.hash,time,AIRDROP_AMOUNT,wallet,state,log], (err, res) => {
            if(err) reject(err);
            resolve();
        })
    });
}

function getLastCheckBlock()
{
    return  new Promise((resolve,reject) => {
        db.query({
                text:"SELECT * from airdrop_vars where code = $1",
                rowMode: 'object',
                values:[VARS_TYPE]
            }, (err, res) => {
            if(err) reject(err);
            if(typeof res.rows[0] == 'undefined' || res.rows.length==0) reject('cant find last_check_block');
            resolve(parseInt(res.rows[0].last_check_block));
        })
    });
}

function setLastCheckBlock(block)
{
    return  new Promise((resolve,reject) => {
        var time = (new Date()).toISOString().replace(/T/,' ');
        db.query(`INSERT INTO airdrop_vars (code,last_check_block,eth_api_key,last_check) values ($1,$2,$3,$4)
                  ON CONFLICT (code)
                  DO UPDATE SET last_check_block = $2, eth_api_key=$3, last_check=$4`
            ,[VARS_TYPE,block,APIKEY,time], (err, res) => {
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
    /* check wallet balance with retry on http error */
    try {
        console.log(`CHECK BALANCE OF WALLET ${wallet}`);
        var balance = await getSwitcheoWalletBalance(wallet,true);
        if(balance.swth==undefined || balance.swth.available < 1){
            console.log(`SEND ${AIRDROP_AMOUNT}swth TO ${wallet} [${tx.hash}]`.green)
            var send_tokens = await sendSwitcheoTokens(wallet,AIRDROP_AMOUNT);
            var send_tx_state = await getSwitcheoTransaction(send_tokens.txhash);
            if(send_tx_state.code=='0'){
                await saveAirdropState('success',wallet,tx);
            } else {
                console.log(`xxx SEND ${AIRDROP_AMOUNT}swth TO ${wallet} xxx`.red)
                await saveAirdropState('airdrop_fail',wallet,tx,e);
            }
        } else {
            console.log(`HIGH BALLANCE OF WALLET ${wallet}`.gray);
            await saveAirdropState('high_balance',wallet,tx);
        }
    }catch(e){
        console.log('AIRDROP ERROR'.red,e,tx.hash);
        sendErrorEmail(e);
        return saveAirdropState('check_balance_error',wallet,tx,e);
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
                    await airdrop(switcheo_wallet,txs[i])
                }
                max_block = Math.max(last_block,parseInt(txs[i].blockNumber));
            }
            await setLastCheckBlock(max_block);
            if(txs.length >= ETH_TRANSACTIONS_LIMIT){
                page++;
                console.log(`NEXT PAGE OF TRANSACTIONS: ${page} [last_blk:${max_block}]`.yellow);
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
        sendErrorEmail(e)
            .then(()=>{process.exit(1);})
            .catch(()=>{process.exit(1);});
    }
}


trackContractTransactions();

process.on('uncaughtException', function(err){
    console.log(err);
    sendErrorEmail(err)
        .then(()=>{process.exit(1);})
        .catch(()=>{process.exit(1);});
});