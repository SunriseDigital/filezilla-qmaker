## FileZilla Qmakerについて

git diffから作成したファイルリストを元にFilleZillaのキュー流し込み用XMLを作成します。Windows/Mac両方に対応しています。

PHPが必要なのでPHPをインストールしてください。

## PHPのインストール

### windows

http://windows.php.net/download/

ここからPHPをダウンロードします。5.3はインストーラーがあるので簡単にインストール可能です。既にもっと新しいバージョンが入っているならそれで問題ありません。

http://windows.php.net/downloads/releases/php-5.3.29-Win32-VC9-x86.msi

最新版をインストールしたい方は[こちら](http://php.net/manual/ja/install.windows.php)を参考にしてください。

コマンドプロンプトを起動し以下のコマンドでインストールを確認して下さい。

```
php -v

PHP 5.3.29 (cli) (built: Aug 15 2014 19:17:16)
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.3.0, Copyright (c) 1998-2014 Zend Technologies
```

### mac

PHP は、OS X バージョン 10.0.0 以降の Mac に標準添付されています。

https://php.net/manual/ja/install.macosx.php




## ファイルリストの作成

ファイルの容量が必要なので作業ツリーにファイルが存在する必要があります。アップ予定のブランチに切り替えて、diffでファイルリストを作成します。

```
git checkout targetBranch
git diff -w master targetBranch --name-only > C:\path\to\filelist.txt
```

## キューファイルの作成

```
php C:\path\to\qmaker.php\
 -f "/path/to/filelist.txt"\
 -s "210.168.71.212,210.168.71.213"\
 -l "C:\path\to\local\repos\root"\
 -r "/path/to/remote/repos/root"\
 -u "user" > C:\path\to\fuzoku-db.xml
```

option | 意味
f | git diffから書きだしたファイルリストのパスを指定します。
s | サーバーのIPアドレスを指定します。カンマ区切りで複数指定可能。
l | ローカルのリポジトリのルートディレクリを指定します。
r | リモートのリポジトリのルートディテクトリを指定します。
u | サーバーにファイルをアップロードするユーザー名を指定します。

