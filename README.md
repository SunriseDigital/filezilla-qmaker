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

公開対象のリポジトリにgitコマンドが実行できるように、リポジトリのディレクトリに移動してください。

```
cd C:\path\to\repos

git status
```

ファイルの容量が必要なので作業ツリーにファイルが存在する必要があります。アップ予定のブランチに切り替えて、diffでファイルリストを作成します。

```
git checkout targetBranch
git diff -w master targetBranch --name-only > C:\path\to\filelist.txt
```

`> C:\path\to\filelist.txt`はgit diffの結果をテキストファイルに書き出します。

## キューファイルの作成

```
php C:\path\to\qmaker.php -f "/path/to/filelist.txt" -s "192.168.0.1,192.168.0.2" -l "C:\path\to\local\repos\root" -r "/path/to/remote/repos/root" -u "user" > C:\path\to\fuzoku-db.xml
```


option | 意味
--- | ---
f | git diffから書きだしたファイルリストのパスを指定します。
s | サーバーのIPアドレスを指定します。カンマ区切りで複数指定可能。
l | ローカルのリポジトリのルートディレクリを指定します。
r | リモートのリポジトリのルートディテクトリを指定します。
u | サーバーにファイルをアップロードするユーザー名を指定します。

## 標準入力からのデータ入力

`git diff`からパイプでデータを渡すことも可能です。`f`オプションをつけるとオプションが優先されますので削除して下さい。

```
git diff -w master targetBranch --name-only | php C:\path\to\qmaker.php -s "192.168.0.1,192.168.0.2" -l "C:\path\to\local\repos\root" -r "/path/to/remote/repos/root" -u "user" > C:\path\to\fuzoku-db.xml
```

## コマンドサンプル集

[プライベートリポジトリ](https://github.com/SunriseDigital/sunrise/wiki/filezila-qmaker%E3%82%B3%E3%83%9E%E3%83%B3%E3%83%89%E3%82%B5%E3%83%B3%E3%83%97%E3%83%AB)にあります。
