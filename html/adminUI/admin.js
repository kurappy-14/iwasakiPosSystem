window.onload = init;




function init() {
    console.log("admin.js loaded");
    loadSettings();
    var menu = document.getElementById("top-menu");
    for (var i = 0; i < menu.children.length; i++) {
        menu.children[i].addEventListener("click", function (event) {

            var href = this.getAttribute("href");
            var id = href.substring(1);
            update_dashbord(id);
            history.pushState(null, null, href);
            event.preventDefault();
        });

    }
    var id = window.location.hash.substring(1);
    if (id != "") {
        update_dashbord(id);
    } else {
        update_dashbord("payment-log");
    }
} // end of init



function update_dashbord(id) {
    var dashbord_contents = document.getElementsByClassName("dashbord-content");
    for (var i = 0; i < dashbord_contents.length; i++) {
        dashbord_contents[i].classList.add("hidden");
    }
    var dashbord = document.getElementById(id);
    dashbord.classList.remove("hidden");


}



// 設定を読み込んで各フィールドに反映する
function loadSettings() {
    fetch('adminAPI/settings_io/settings_read.php')
        .then(response => response.json())
        .then(data => {
            // 支払い方法チェックボックス
            document.getElementById('cash').checked = data.paytype.cash;
            document.getElementById('JCB').checked = data.paytype.JCB;
            document.getElementById('Visa').checked = data.paytype.Visa;
            document.getElementById('QUICPay').checked = data.paytype.QUICPay;
            document.getElementById('iD').checked = data.paytype.iD;
            document.getElementById('IC').checked = data.paytype.IC;
            document.getElementById('PayPay').checked = data.paytype.PayPay;
            document.getElementById('others').checked = data.paytype.others;

            // プリンター設定
            document.getElementById('printer').checked = data.printer;

            // 環境設定
            document.getElementById('store-name').value = data.STORENAME;
            document.getElementById('token').value = data.environment.TOKEN;
            document.getElementById('device-id').value = data.environment.DEVICE;
            document.getElementById('printer-ip').value = data.environment.PrinterIP;
        })
        .catch(error => console.error('Error loading settings:', error));
}

// 設定を保存する
function updateSettings() {
    const updatedSettings = {
        paytype: {
            cash: document.getElementById('cash').checked,
            JCB: document.getElementById('JCB').checked,
            Visa: document.getElementById('Visa').checked,
            QUICPay: document.getElementById('QUICPay').checked,
            iD: document.getElementById('iD').checked,
            IC: document.getElementById('IC').checked,
            PayPay: document.getElementById('PayPay').checked,
            others: document.getElementById('others').checked,
        },
        printer: document.getElementById('printer').checked,
        STORENAME: document.getElementById('store-name').value, // STORENAMEの更新
        environment: {
            TOKEN: document.getElementById('token').value,
            DEVICE: document.getElementById('device-id').value,
            PrinterIP: document.getElementById('printer-ip').value,
        }
    };

    fetch('adminAPI/settings_io/settings_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(updatedSettings)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('設定が更新されました');
            } else {
                alert('設定の更新に失敗しました');
            }
        })
        .catch(error => console.error('Error updating settings:', error));
}

// トークンやデバイスIDの表示/非表示を切り替える関数
function toggleVisibility(inputId) {
    const inputField = document.getElementById(inputId);
    const currentType = inputField.type;

    if (currentType === 'password') {
        inputField.type = 'text'; // 表示に切り替え
    } else {
        inputField.type = 'password'; // 非表示に切り替え
    }

    // ボタンのテキストを切り替え
    const button = document.getElementById(inputId + '-toggle');
    button.classList.toggle('hide');
    button.classList.toggle('show');
    if (button.classList.contains('hide')) {
        button.textContent = '表示';
    } else {
        button.textContent = '非表示';
    }
}




function testPrinterIP(){
    fetch('adminAPI/settings_io/printTest.php')
    .then(
        alert('印刷テストを行いました')
    )
}