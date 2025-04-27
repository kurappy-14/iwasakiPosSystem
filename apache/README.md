# ログインページについて

- 設定について
.envに環境変数があるのでこれで制御している見たらわかる

- 新しくページを追加する際にはセッションを生成していないユーザーをログイン画面にリダイレクトためにauth.phpをインクルードさせてください
phpの頭にこれ追加するだけなので

```php
<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>
```
