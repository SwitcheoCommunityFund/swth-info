const nodemailer = require("nodemailer");


const mailer_login = 'some_login@gmail.com';
const mailer_name = 'SWTH.INFO';
const mailer_password = '*****';
const mailer_receivers = ['admin_mail@gmail.com'];



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

    return transporter.sendMail({
        from    : `"${mailer_name}" <${mailer_login}>`,
        to      : mailer_receivers.join(", "),
        subject : data.subj+' '+time,
        text    : data.body
    });
}


let data = Buffer.from(process.argv[2], 'base64').toString('ascii');

sendErrorEmail(JSON.parse(data));