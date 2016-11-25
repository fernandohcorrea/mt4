<?php

namespace Fhc\SecurityShell;

use Fhc\Exception as Exc;

/**
 * Description of SshConnection
 *
 * @author Fernando H Corrêa
 */
class SshConnection
{

    private $cfg;
    private $ssh_host;
    private $ssh_port;
    private $ssh_server_fp;
    private $ssh_auth_user;
    private $ssh_auth_path;
    private $ssh_auth_pub;
    private $ssh_auth_priv;
    private $ssh_auth_pass;
    private $connection;

   
    public function __construct($ssh_auth_user, $ssh_host, $ssh_server_fp = NULL, $ssh_port = 22, $ssh_auth_pass = NULL)
    {
        $this->ssh_auth_user = $ssh_auth_user;
        $this->ssh_host = $ssh_host;
        $this->ssh_server_fp = (!empty($ssh_server_fp)) ? $ssh_server_fp : NULL;
        $this->ssh_port = (!empty($ssh_port)) ? $ssh_port : 22;
        $this->ssh_auth_pass = (!empty($ssh_auth_pass)) ? $ssh_auth_pass : NULL;
        $this->parseCfg();
        $this->connect();
    }

    private function parseCfg()
    {
        $this->cfg = \Fhc\Config\Loader::get('ssh_*');

        $required = [
            'ssh_auth_path',
            'ssh_auth_pub',
            'ssh_auth_priv'
        ];

        foreach ($required as $key) {
            if (empty($this->cfg[$key])) {
                throw new Exc\RuntimeException("Config Error[{$key}] is required ");
            }
        }

        $this->ssh_auth_path = APPATH . DS . $this->cfg['ssh_auth_path'];
        $this->ssh_auth_pub = realpath(
                sprintf($this->cfg['ssh_auth_pub'], $this->ssh_auth_path, $this->ssh_auth_user)
        );
        $this->ssh_auth_priv = realpath(
                sprintf($this->cfg['ssh_auth_priv'], $this->ssh_auth_path, $this->ssh_auth_user)
        );
    }

    private function connect()
    {
        $callbacks = array(
            'ignore' => array($this, 'callbackSshDisconnect'),
            'debug' => array($this, 'callbackSshDisconnect'),
            'macerror' => array($this, 'callbackSshDisconnect'),
            'disconnect' => array($this, 'callbackSshDisconnect'),
        );

        $methods = array(
            'kex' => 'diffie-hellman-group1-sha1',
            'client_to_server' => array(
                'crypt' => '3des-cbc',
                'comp' => 'none'),
            'server_to_client' => array(
                'crypt' => 'aes256-cbc,aes192-cbc,aes128-cbc',
                'comp' => 'none'));

        /**
         * Caso ssh2_connect não funcione de algum modo e não retorne o motivo
         * use esse comando no terminal : 
         *    # setsebool -P httpd_can_network_connect 1
         * Isso vai liberar o acesso do httpd para as connexões de rede no SELinux
         */
        if (!($this->connection = ssh2_connect($this->ssh_host, $this->ssh_port, $methods, $callbacks))) {
            throw new Exc\RuntimeException('Cannot connect to server');
        }

        $fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
        if (!is_null($this->ssh_server_fp) && strcmp($this->ssh_server_fp, $fingerprint) !== 0) {
            throw new Exc\RuntimeException('Unable to verify server identity!');
        }

        if (!ssh2_auth_pubkey_file($this->connection, $this->ssh_auth_user, $this->ssh_auth_pub, $this->ssh_auth_priv, $this->ssh_auth_pass)) {
            throw new Exc\RuntimeException('Autentication rejected by server');
        }
    }

    public function callbackSshDisconnect($reason, $message, $language)
    {
        $error = sprintf("Server disconnected with reason code [%d] and message: %s\n", $reason, $message);
        error_log($error);
    }

    public function exec($cmd)
    {
        if (!($stream = ssh2_exec($this->connection, $cmd))) {
            throw new Exc\RuntimeException('SSH command failed');
        }
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream, 4096)) {
            $data .= $buf;
        }
        fclose($stream);

        return $data;
    }

    public function disconnect()
    {
        if ($this->connection) {
            $this->exec('echo "EXITING" && exit;');
            $this->connection = null;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }

}
