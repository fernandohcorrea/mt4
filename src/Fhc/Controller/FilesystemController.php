<?php

namespace Fhc\Controller;

use Fhc\Bootstrap\DB\Manager;
/**
 * Description of HomeController
 *
 * @author fcorrea
 */
class FilesystemController extends BaseLayoutController
{

    private $cfg;

    public function __construct()
    {
        $this->cfg = \Fhc\Config\Loader::get('filesystem_*');
    }

    public function index()
    {
        $this->view->render();
    }

    public function load()
    {
        $json['status'] = true;
        $node = null;

        $get = $this->getParamsGet();
        if (!empty($get['node'])) {
            $node = base64_decode($get['node']);
        }

        $files_node = $this->loadFilesystemNode($node);

        if (!empty($files_node['currentDirNode'])) {
            $json['currentDirNode'] = $files_node['currentDirNode'];
        }

        if (!empty($files_node['list_files'])) {
            $json['list_files'] = $files_node['list_files'];
        }

        echo json_encode($json);
    }

    public function download()
    {
        $this->view = null;

        $params = $this->getParamsUrl();
        $nodeB64 = array_shift($params);
        $fileInfo = $this->getNodeInfoBase64Decode($nodeB64);

        $file_path = $fileInfo->getPathname();
        $filename = $fileInfo->getFilename();
        $size = $fileInfo->getFileInfo()->getSize();


        $maxRead = 1 * 1024 * 1204;

        $fh = fopen($file_path, 'r');

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $size);

        while (!feof($fh)) {
            echo fread($fh, $maxRead);
            ob_flush();
        }

        exit;
    }

    public function upload()
    {
        $jsonOut = [];
        
        $get = $this->getParamsGet();
        $post = $this->getParamsPost();
        $files = $this->getParamsFiles();
        
        try {
            
            if (is_dir($this->cfg['filesystem_root_path'])) {
                $root_path = realpath($this->cfg['filesystem_root_path']);
                $root_name = basename($root_path);
            } else {
                $root_path = realpath(implode(DS, [
                    APPATH,
                    $this->cfg['filesystem_root_path']
                ]));
                $root_name = basename($root_path);
            }
            
            if(empty($get['node'])){
                throw new \Fhc\Exception\RuntimeException('Error path');
            }
            
            $fileInfo = $this->getNodeInfoBase64Decode($get['node']);
            
            $toPath = $fileInfo->getPathname();
            
            if(!empty($post['directory'])){
                $mkdir = $toPath. DS. $post['directory'];
                if(!is_dir($mkdir) && !is_file($mkdir)){
                    mkdir($mkdir);
                }
                $toPath = $mkdir;
            }
            
            $root_name = basename($root_path);

            $conn = Manager::getInstance()->getConn('mt4');
            
            foreach ($files as $file) {
    
                $new_file_path = $toPath . DS . $file['name'];
                $SplFileInfo = new \SplFileInfo($new_file_path);
                $chk_file_moved = move_uploaded_file($file['tmp_name'], $new_file_path);
                
                if($chk_file_moved){
                    
                    $name = $SplFileInfo->getFilename();
                    $chk_method = 'MD5SUM';
                    $name_md5 = md5(str_replace($root_path, '', $new_file_path));
                    $md5_file = md5($new_file_path);

                    $st = $conn->prepare("insert into files('name','chk_method', 'name_md5', 'md5_file') values (?,?,?,?)");
                    $st->execute(array($name, $chk_method, $name_md5, $md5_file));
                    
                }
                
            }

            $jsonOut['msg'] = 'Sucesso';
            $jsonOut['status'] = true;
        } catch (\Exception $exc) {
            error_log("Erro de teste de conexao ssh({$exc->getMessage()})");
            $jsonOut['msg'] = 'Erro de upload' .
            $jsonOut['status'] = false;
        }

        echo json_encode($jsonOut);
    }

    /**
     * @param string $node
     * @return \SplFileInfo
     * @throws \Fhc\Exception\RuntimeException
     */
    private function getNodeInfoBase64Decode($node)
    {

        if (is_dir($this->cfg['filesystem_root_path'])) {
            $root_path = realpath($this->cfg['filesystem_root_path']);
            $root_name = basename($root_path);
        } else {
            $root_path = realpath(implode(DS, [
                APPATH,
                $this->cfg['filesystem_root_path']
            ]));
            $root_name = basename($root_path);
        }

        $path = $root_path;
        $node = base64_decode($node);
        $path = $root_path . str_replace($root_name, '', $node);

        if (strpos(realpath($path), $root_path) === false) {
            throw new \Fhc\Exception\RuntimeException("Attempted invasion in progress($path).");
        }

        return new \SplFileInfo($path);
    }

    private function loadFilesystemNode($node)
    {
        $ret = [];

        if (is_dir($this->cfg['filesystem_root_path'])) {
            $root_path = realpath($this->cfg['filesystem_root_path']);
            $root_name = basename($root_path);
        } else {
            $root_path = realpath(implode(DS, [
                APPATH,
                $this->cfg['filesystem_root_path']
            ]));
            $root_name = basename($root_path);
        }

        $path = $root_path;
        $current = $root_name;

        if (!empty($node)) {
            $path = $root_path . str_replace($root_name, '', $node);
            $current = $root_name . str_replace($root_path, '', $path);
        }

        $SplDirInfo = new \SplFileInfo($path);

        $currentDirNode = [
            'node' => base64_encode($current),
            'file_name' => $SplDirInfo->getFilename()
        ];

        $ret['currentDirNode'] = $currentDirNode;


        if (strpos(realpath($path), $root_path) === false) {
            throw new \Fhc\Exception\RuntimeException("Attempted invasion in progress($path).");
        }

        $fileInt = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);

        foreach ($fileInt as $key => $file) {

            $node = $root_name . str_replace($root_path, '', $file->getPathname());
            $node = base64_encode($node);

            $file_date = [
                'node' => $node,
                'file_name' => $file->getFilename(),
                'is_dir' => $file->isDir(),
                'is_file' => !$file->isDir(),
                'cls_icon' => ($file->isDir()) ? 'fa-folder' : 'fa-file',
                'checked' => false
            ];

            $ret['list_files'][] = $file_date;
        }


        return $ret;
    }

}
