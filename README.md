## FileZilla Qmakerについて

git diffから作成したファイルリストを元にFilleZillaのキュー流し込み用XMLを作成します。


## ファイルリストの作成

ファイルの容量が必要なので作業ツリーにファイルが存在する必要があります。アップ予定のブランチに切り替えて、diffでファイルリストを作成します。

```
git checkout targetBranch
git diff -w master targetBranch --name-only > /path/to/filelist.txt
```

## キューファイルの作成

```
php /path/to/qmaker.php\
 -f "/path/to/filelist.txt"\
 -s "210.168.71.212,210.168.71.213"\
 -l "/path/to/local/repos/root"\
 -r "/path/to/remote/repos/root" > /Users/masamoto/Documents/temp/fuzoku-db.xml
```

option | 意味
f | git diffから書きだしたファイルリストのパスを指定します。
s | サーバーのIPアドレスを指定します。カンマ区切りで複数指定可能。
l | ローカルのリポジトリのルートディレクリを指定します。
r | リモートのリポジトリのルートディテクトリを指定します。
u | サーバーにファイルをアップロードするユーザー名を指定します。

