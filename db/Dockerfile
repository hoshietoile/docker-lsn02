from mysql:8.0

workdir /app

env MYSQL_DATABASE db
env MYSQL_USER user
env MYSQL_PASSWORD root
env MYSQL_ROOT_PASSWORD root

expose 3306

cmd ["--character-set-server=utf8mb4",  "--collation-server=utf8mb4_unicode_ci", "--default-authentication-plugin=mysql_native_password"]