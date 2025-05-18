<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cash register</title>
        <link rel="stylesheet" href="css/registerB.css" id="css">
    </head>
    <body>
        <div class="color1">
            <div id="header">
                <div id="header1">
                    <label class="toggle-button"><input type="checkbox"  id="toggle"/></label>
                    <h1 class="title">メニュー</h1>
                </div>
                <div id="header2"></div>
            </div>
            <div id="menu"></div>
        </div>
        <div id="payment" class="hidden">
            <span id="close">&times;</span>
            <p class="paytitle">支払方法の選択</p>
            <div class="paybox" id="cash">
                <p>現金</p>
            </div>
            <div class="paybox" id="JCB">
                <p>JCB/Diners Club/Discover</p>
            </div>
            <div class="paybox" id="Visa">
                <p>Visa/Mastercard/Amex</p>
            </div>
            <div class="paybox" id="QUICPay">
                <p>QUICPay</p>
            </div>
            <div class="paybox" id="iD">
                <p>iD</p>
            </div>
            <div class="paybox" id="IC">
                <p>交通系IC</p>
            </div>
            <div class="paybox" id="PayPay">
                <p>PayPay</p>
            </div>
            <div class="paybox" id="others">
                <p>その他</p>
            </div>
        </div>
        <div id="paywait" class="hidden">
            <p id="waittitle">＜JCB/DinersClub/Discover＞</p>
            <p id="waittotal">合計金額</p>
              <div class="spin">
                <div class="circle"></div>
              </div>
              <div class="waittext">
                <h2>支払いが終了したら完了を押してください</h2>
              </div>
            <div class="waitbutton">
                <button id="payreturn">戻る</button>
                <button id="paycomplete">完了</button>
            </div>
        </div>
        <div id="waitingfor" class="hidden">
            <p class="wait">接続中</p>
            <div class="spin">
                <div class="circle"></div>
              </div>
        </div>
        <div id="purchase" class="hidden">
            <div class="circle2">
                <p class="wait2">購入手続き完了</p>
                <p class="wait3" id="textdone">案内番号を渡してください</p>
                <span class="dli-check-circle"><span></span></span>
            </div>
        </div>
        <div id="callidinput" class="hidden">
            <p>呼び出し番号を入力してください</p>
            <div class="inputcell">
                <input type="input" id="inputid" size="4" maxlength="4" placeholder="CALLID HERE" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '');"></input>
                <button id="Confirmid">確定</button>
            </div>
            <div class="keybord-wrapper">
                <!-- 一番上の段 -->
                <div class="keys">
                  <input type="button" value="7" onclick="typed(event)" />
                  <input type="button" value="8" onclick="typed(event)" />
                  <input type="button" value="9" onclick="typed(event)" />
                  <input type="button" value="←" onclick="typed(event)" />
                </div>
                <!-- 真ん中の段 -->
                <div class="keys">
                  <input type="button" value="4" onclick="typed(event)" />
                  <input type="button" value="5" onclick="typed(event)" />
                  <input type="button" value="6" onclick="typed(event)" />
                  <input type="button" value="C" onclick="typed(event)" />
                </div>
                <!-- 一番下の段 -->
                <div class="keys">
                  <input type="button" value="1" onclick="typed(event)" />
                  <input type="button" value="2" onclick="typed(event)" />
                  <input type="button" value="3" onclick="typed(event)" />
                  <input type="button" value="0" onclick="typed(event)"/>
                </div>
              </div>
        </div>
    </body>
    <script src="js/register.js"></script>
</html>