#!/bin/bash
set -e

# Apacheの設定ファイル
APACHE_CONFIG="/etc/apache2/sites-available/000-default.conf"

# 環境変数を確認
if [ "$LOGIN_ENABLED" == "true" ]; then
  echo "ログイン機能を有効化中..."

  # パスワードをハッシュ化 (stderr を無視)
  HASHED_PASSWORD=$(php -r "echo password_hash('${LOGIN_PASSWORD}', PASSWORD_BCRYPT);" 2>/dev/null)

  # ユーザー名とハッシュ化されたパスワードをJSONファイルに保存
  echo "{\"username\": \"${LOGIN_USER}\", \"password\": \"${HASHED_PASSWORD}\"}" > /hashed.json

  # Apache の DirectoryIndex を login.php に設定
  echo "ログイン画面を有効化中、Apache の設定を更新します..."
  sed -i 's|DirectoryIndex .*|DirectoryIndex login.php|' $APACHE_CONFIG
  # Apache設定をログイン有効用に書き換え
  cat <<EOF > "$APACHE_CONFIG"
<VirtualHost *:80>
    DocumentRoot /var/www/html/
    DirectoryIndex login.php

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    RewriteEngine On
    RewriteCond %{HTTP_COOKIE} !PHPSESSID=
    RewriteRule ^/$ /login.php [L,R=302]
</VirtualHost>
EOF

else
  echo "ログイン機能は無効です。"
fi

chmod 777 /var/www/html/setting.json
composer install
exec apache2-foreground
