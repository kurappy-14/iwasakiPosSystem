fetch("adminAPI/paylog/paylog.php")
    .then(response => response.text())
    .then(text => {
        json = JSON.parse(text);

        let sum_element = document.getElementById("sum");
        sum_element.innerHTML = `合計売り上げ: ${json.sum}円 　　　(現金: ${json.payment_type.現金}円)`;
        document.getElementById("payment-log").appendChild(sum_element);

        let table = document.createElement("table");
        let header = table.createTHead();
        let headerRow = header.insertRow();
        let headerCell1 = headerRow.insertCell();
        let headerCell2 = headerRow.insertCell();
        let headerCell3 = headerRow.insertCell();
        let headerCell4 = headerRow.insertCell();
        headerCell1.innerHTML = "注文番号";
        headerCell2.innerHTML = "金額";
        headerCell3.innerHTML = "日時";
        headerCell4.innerHTML = "詳細";




        let body = table.createTBody();
        for (let i = 0; i < json.data.length; i++) {
            let row = body.insertRow();
            let cell1 = row.insertCell();
            let cell2 = row.insertCell();
            let cell3 = row.insertCell();
            let cell4 = row.insertCell();
            cell1.innerHTML = json.data[i].order_id;
            if (json.data[i].status == 1) {
                cell1.style.color = "red";
            }
            cell2.innerHTML = json.data[i].price;
            cell3.innerHTML = json.data[i].time;

            let addithional_info = document.createElement("b");
            addithional_info.style.cursor = "pointer";
            addithional_info.style.color = "blue";
            addithional_info.innerHTML = "詳細";
            addithional_info.onclick = function () {
                // ここに詳細の表示処理を書く
                // 新規ウィンドウで詳細を表示する
                window.open("adminAPI/paylog/詳細.php?order_id=" + json.data[i].order_id, null, "width=400, height=500");

            }
            cell4.appendChild(addithional_info);

        }

        document.getElementById("payment-log").appendChild(table);
    });