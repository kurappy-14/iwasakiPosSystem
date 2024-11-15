window.addEventListener('load', fetchCategories);
// Categoryを取得して表示する関数
function fetchCategories() {
    fetch('adminAPI/settings_io/settings_read.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            let categories = data.Category;

            // 重みで昇順ソート、0は最後に表示されるようにする
            const sortedCategories = categories
                .map((category, index) => ({ ...category, originalIndex: index })) // 元のインデックスを保持
                .sort((a, b) => {
                    if (a.weight === b.weight) return 0;
                    if (a.weight === 0) return 1;
                    if (b.weight === 0) return -1;
                    return a.weight < b.weight ? -1 : 1;
                });

            // カテゴリの表示部分
            const categoryList = document.getElementById('category-list');
            categoryList.innerHTML = '';  // 既存のリストをクリア

            sortedCategories.forEach((category, index) => {
                const listItem = document.createElement('li');
                listItem.classList.add('category-item');
                listItem.innerHTML = `
                    <input type="text" class="category-name" value="${category.name}" data-index="${category.originalIndex}" />
                    <input type="number" class="category-weight" value="${category.weight}" data-index="${category.originalIndex}" />
                    <button onclick="deleteCategory(${category.originalIndex})">削除</button>
                `;
                categoryList.appendChild(listItem);
            });
        })
        .catch(error => console.error('Error:', error));
}

// Categoryの変更を一括で保存する関数
function saveCategories() {
    const categoryItems = document.querySelectorAll('.category-item');
    const updatedCategories = [];

    categoryItems.forEach(item => {
        const name = item.querySelector('.category-name').value;
        const weight = parseInt(item.querySelector('.category-weight').value, 10);
        updatedCategories.push({ name, weight });
    });

    // 保存するデータを作成
    fetch('adminAPI/settings_io/settings_save.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ Category: updatedCategories }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('保存しました！');
                fetchCategories();  // 再度カテゴリを読み込み直す
            } else {
                alert('保存に失敗しました。');
            }
        })
        .catch(error => console.error('Error:', error));
}

// 新しいカテゴリを追加する関数
function addCategory() {
    const categoryName = document.getElementById('new-category-name').value;
    const categoryWeight = parseInt(document.getElementById('new-category-weight').value, 10);

    if (!categoryName || isNaN(categoryWeight)) {
        alert('カテゴリ名と重みを正しく入力してください！');
        return;
    }

    // 新しいカテゴリを作成
    const newCategory = { name: categoryName, weight: categoryWeight };

    // 既存のカテゴリを取得して追加
    fetch('adminAPI/settings_io/settings_read.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            data.Category.push(newCategory); // 新しいカテゴリを追加

            // カテゴリを保存
            fetch('adminAPI/settings_io/settings_save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ Category: data.Category }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('カテゴリが追加されました！');
                        fetchCategories();  // カテゴリを再度表示
                    } else {
                        alert('カテゴリ追加に失敗しました。');
                    }
                })
                .catch(error => console.error('Error:', error));
        })
        .catch(error => console.error('Error:', error));
}

// カテゴリを削除する関数
function deleteCategory(index) {
    fetch('adminAPI/settings_io/settings_read.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // 削除するカテゴリを取り除く
            data.Category.splice(index, 1);

            // 更新されたカテゴリを保存
            fetch('adminAPI/settings_io/settings_save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ Category: data.Category }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('カテゴリが削除されました！');
                        fetchCategories();  // カテゴリを再度表示
                    } else {
                        alert('カテゴリ削除に失敗しました。');
                    }
                })
                .catch(error => console.error('Error:', error));
        })
        .catch(error => console.error('Error:', error));
}
