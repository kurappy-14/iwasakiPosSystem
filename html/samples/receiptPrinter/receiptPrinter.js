// receiptPrinter.js
const URL = 'http://192.168.137.25/cgi-bin/epos/service.cgi?devid=local_printer&timeout=10000';
const TIMEOUT = 5000
/*

README

// 番号札の発行リクエスト
var req = new PrintRequest( 256, [{name:"hoge", count:1}, {name:"foo", count:1}, ...] )
//この時点で送信は完了している

await req.join() で、タイムアウト又はレスポンスが返ってくるまで待機できる
req.status でレスポンスのステータスコードが得られる　レスポンスがない場合は0
req.readyState で https://developer.mozilla.org/ja/docs/Web/API/XMLHttpRequest/readyState が取得できる。
なお追加で、-1でタイムアウトを表現している

*/

// Gen

class Numberreceipt{
    number;
    order = [];
    constructor(number, order){
        if(number==undefined || order==undefined){
            throw new Error('Parameter undefined');
        }else if(! order instanceof Object){
            throw new Error('order must be instanceof Object');
        }else if(!( order.length>0 && order[0] instanceof Object )){
            throw new Error('order object is invalid');
        }
        this.number = number;
        this.order = order;
    }

    get eposxml() {
        const req = 
            '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">' +
            '<s:Body>' +
            '<epos-print xmlns="http://www.epson-pos.com/schemas/2011/03/epos-print">' +
            '<text lang="ja" />'+
            '<text font="font_a"/>'+
            '<text align="center"/>'+
            '<feed unit="15"/>'+
            '<text dw="true" dh="true"/>'+
            '<text>◆ ギョウザ亭 ◆&#10;</text>'+
            '<text dw="false" dh="false"/>'+
            '<text>受け渡し用控え&#10;ご来店誠にありがとうございます&#10;</text>'+
            '<feed unit="15"/>'+
            '<text>------------------------------&#10;</text>'+
            '<feed unit="15"/>'+
            '<text align="center"/>'+
            this.formatArray(this.order)+
            '<feed unit="24"/>'+
            '<text>番号札はお品物交換時に&#10;回収させていただきます&#10;</text>'+
            '<feed unit="15"/>'+
            '<text>------------------------------&#10;</text>'+
            '<feed unit="15"/>'+
            '<text dw="true" dh="true"/>'+
            '<text font="font_b"/>'+
            '<text>お客様のご注文番号&#10;</text>'+
            '<text width="8" height="5"/>'+
            '<text font="font_a"/>'+
            '<text reverse="false" ul="false" em="false" color="color_1"/>'+
            '<text>' + this.number + '&#10;</text>'+
            '<feed unit="15"/>'+
            '<cut/>' +
            '</epos-print>'+
            '</s:Body>'+
            '</s:Envelope>';
        
        return req;
    }

    formatObjectToString(obj) {
        const name = obj.name;
        const count = obj.count.toString();
        
        const nameWidth = Array.from(name).reduce((width, char) => {
            return width + (char.match(/[ -~｡-ﾟ]/) ? 1 : 2);
        }, 0);
        const totalWidth = 30;
        const spacesNeeded = totalWidth - nameWidth - count.length;
        
        return '<text>' + name + ' '.repeat(spacesNeeded) + count + '点&#10;</text>';
    }

    formatArray(arr) {
        return arr.map(this.formatObjectToString).join('');
    }
}
// Request

export class PrintRequest{

    status = 0;
    readyState;

    constructor(number, order){
        // [{name:"hoge", count:1}, {...}]
        if(number==undefined || order==undefined){
            throw new Error('Parameter undefined');
        }else if(! order instanceof Object){
            throw new Error('order must be instanceof Object');
        }else if(!( order.length>0 && order[0] instanceof Object )){
            throw new Error('order object is invalid');
        }

        const req = new Numberreceipt(number, order).eposxml;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', URL, true);
        xhr.setRequestHeader('Content-Type', 'text/xml; charset=utf-8');
        xhr.setRequestHeader('If-Modified-Since', 'Thu, 01 Jan 1970 00:00:00 GMT');
        xhr.setRequestHeader('SOAPAction', '""');
        xhr.onreadystatechange = () => {

            // Receive response document
            this.readyState = xhr.readyState;
            if (xhr.readyState == 4) {

                // Parse response document
                this.status = xhr.status
                if (xhr.status == 200) {
                    // alert(xhr.responseXML.getElementsByTagName('response')[0].getAttribute('success'));
                    console.log("success")
                }
                else {
                    // alert('Network error occured.');
                    console.log("failure")
                }
            }
        };
        xhr.timeout=TIMEOUT;
        xhr.ontimeout = () => {
            this.readyState = -1;
            // console.log("timeout")
        }

        xhr.send(req);
        console.log("send");
    }

    async join() {
        while (this.readyState !== 4 && this.readyState !== -1) {
            await new Promise(resolve => setTimeout(resolve, 100)); // 100ミリ秒待機
        }
        if (this.readyState == -1) return false;
        else return true;
    }
}