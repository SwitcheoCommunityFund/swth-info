const { WalletClient, RestClient, WsClient } = require('tradehub-api-js')
const express = require('express');
var app = express();
const bodyParser = require("body-parser");
app.use(bodyParser.urlencoded({extended: true}));
//app.use(bodyParser.json());


const network = 'MAINNET'
const hostname = '127.0.0.1';
const port = 8080;
const wallet_addr = 'swth1oooooooooooooooooooooooooooooooooooooo';
const privateKey = '*****';
const amountLim = 3;
const mnemonics = '*****';


app.listen(port);

app.post('/', function(req, res)
{
    try {
        sendTokens(req.body,res);
    } catch (e) {
        res.json({status:'fail',description:'unknown error',error:e});
    }
});

console.log(`Server running at http://${hostname}:${port}/`);


async function sendTokens(params,res)
{
    const wallet = await WalletClient.connectMnemonic(mnemonics, network);
    const rest = new RestClient({ wallet, network });

    if(empty(params.wallet)){
        return res.json({"status":"fail","description":"destination wallet must not be an empty"});
    }

    if(empty(params.amount)){
        return res.json({"status":"fail","description":"amount must not be an empty"});
    }

    if(params.amount>3){
        return res.json({"status":"fail","description":"faucet limit is "+amountLim+"SWTH"});
    }

    params.amount = Math.max(params.amount,1);

    var message = {
        amount : [{ denom: 'swth', amount: (params.amount*100000000)+''}],
        from_address : wallet_addr,
        to_address : params.wallet
    }
    var apiReq = await rest.send(message);
    console.log(apiReq);

    res.json({status:'success',api_request:apiReq});
}

function empty(d){
    return (d == null || d == undefined);
}