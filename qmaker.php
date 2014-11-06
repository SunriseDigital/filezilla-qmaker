<?php
class FileZilla{
  private $options;

  private $main_wrapper = <<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<FileZilla3>
    <Queue>
{servers}
    </Queue>
</FileZilla3>
EOF;

  private $server_wrapper = <<<EOF
        <Server>
            <Host>{ip_address}</Host>
            <Port>22</Port>
            <Protocol>1</Protocol>
            <Type>0</Type>
            <User>{user}</User>
            <Pass />
            <Logontype>1</Logontype>
            <TimezoneOffset>0</TimezoneOffset>
            <PasvMode>MODE_DEFAULT</PasvMode>
            <MaximumMultipleConnections>0</MaximumMultipleConnections>
            <EncodingType>Auto</EncodingType>
            <BypassProxy>0</BypassProxy>
            <Name>{server_name}</Name>
{files}
        </Server>
EOF;

  private $file_wrapper = <<<EOF
            <File>
                <LocalFile>{local_path}</LocalFile>
                <RemoteFile>{remote_file}</RemoteFile>
                <RemotePath>{remote_path}</RemotePath>
                <Download>0</Download>
                <Size>{file_size}</Size>
                <DataType>{data_type}</DataType>
            </File>
EOF;

  private $options_msg = array(
    'f' => 'git diff -w master targetBranch --name-only で書きだしたファイル名のファイルパス',
    's' => 'サーバーのIPアドレス',
    'l' => '自分のパソコンのリポジトリベースパス',
    'r' => 'サーバーのリポジトリベースパス',
    'u' => 'ファイルをアップロードするユーザー名'
  );

  private function getRequireOption($name){
    $value = @$this->options[$name];
    if(empty($value)){
      $this->error('-'.$name.'　のオプションに'.$this->options_msg[$name].'を指定してください');
    }

    return $value;
  }

  private function getFiles(){
    $path = $this->getRequireOption('f');
    if(!is_readable($path)){
      $this->error($path.'が読めません。');
    }

    return explode(PHP_EOL, file_get_contents($path));
  }

  private function error($msg){
    $this->echoStdErr($msg);
    die();
  }

  private function buildRemotePath($file){
    $remote_file = $this->getRequireOption('r');
    if(strpos($remote_file, "/") !== false){
      $dir_sep = '/';
    } else {
      $dir_sep = '\\';
    }

    $remote_file .= $dir_sep.$file;

    $path = '';
    $paths = explode($dir_sep, $remote_file);
    foreach($paths as $key => $chunk){
      if(count($paths) - 1 == $key) break;

      if($path !== ''){
        $path .= ' ';
      }
      
      if($chunk === ''){
        $path .= '1 0';
      } else {
        $path .= strlen($chunk).' '.$chunk;
      }

    }

    return $path;
  }

  private function replaceWrapperTemplate($template, array $params){
    foreach($params as $key => $value){
      $template = str_replace('{'.$key.'}', $value, $template);
    }

    return $template.PHP_EOL;
  }

  private function notice($msg){
    $this->echoStdErr($msg);
  }

  private function getDataType($file){
    //画像は1
    if(exif_imagetype($file)){
      return 1;
    } else {//一般ファイルは0
      return 0;
    }
  }

  private function buildFilesWrapper($file){
    $paths = explode(DIRECTORY_SEPARATOR, $file);
    $local_path = $this->getRequireOption('l').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $file);

    if(!file_exists($local_path)){
      $this->notice($local_path ." はファイルが存在しません");
      return '';
    }

    $file_size = @filesize($local_path);
    if ($file_size === 0){
      $this->notice($local_path ." のサイズは0です。");
    }

    $params = array(
      'local_path' => $local_path,
      'remote_file' => $paths[count($paths) - 1],
      'remote_path' => $this->buildRemotePath($file),
      'file_size' => $file_size,
      'data_type' => $this->getDataType($local_path),
    );

    return $this->replaceWrapperTemplate($this->file_wrapper, $params);
  }

  protected function echoStdErr($value){
    fwrite(STDERR, $value.PHP_EOL);
  }
  
  protected function echoStdOut($value){
    fwrite(STDOUT, $value.PHP_EOL);
  }

  public function __construct(){
    $this->options = getopt("f:s:l:r:u:");
  }

  public function main(){
    $files = $this->getFiles();
    $files_text = '';
    $file_count = 0;
    foreach($files as $file){
      if(empty($file)) continue;
      $files_text .= $this->buildFilesWrapper($file);
      ++$file_count;
    }

    $server_text = '';
    $server_count = 0;
    foreach(explode(',', $this->getRequireOption('s')) as $ip){
      $params = array(
        'ip_address' => $ip,
        'server_name' => str_replace('.', '_', $ip).'_server',
        'files' => $files_text,
        'user' => $this->getRequireOption('u'),
      );

      $server_text .= $this->replaceWrapperTemplate($this->server_wrapper, $params);
      ++$server_count;
    }

    $main = $this->replaceWrapperTemplate($this->main_wrapper, array('servers' => $server_text));
    $this->echoStdOut($main);
    $this->echoStdErr(sprintf('%d個のファイルを%dつのサーバーにアップします', $file_count, $server_count));
  }
}

$filezilla = new FileZilla();
$filezilla->main();