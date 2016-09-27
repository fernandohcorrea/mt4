<?php

namespace Fhc\Controller;

/**
 * Description of HomeController
 *
 * @author fcorrea
 */
class SshController extends BaseLayoutController
{

    public function index()
    {
        $this->view->render();
    }

    public function testconnection()
    {
        $post = $this->getParamsPost();
        $jsonOut = [];
        try {

            $sshConnc = new \Fhc\SecurityShell\SshConnection(
                    $post['user'], $post['host'], $post['fingerprint'], $post['port'], $post['password']
            );

            $_SESSION['ssh_connection_data'] = $post;

            $jsonOut['msg'] = 'Sucesso';
            $jsonOut['status'] = true;
        } catch (\Exception $exc) {
            error_log("Erro de teste de conexao ssh({$exc->getMessage()})");
            $jsonOut['msg'] = 'Erro de teste de conexão ssh' .
                    $jsonOut['status'] = false;
        }

        echo json_encode($jsonOut);
    }

    public function loadconnections()
    {
        $jsonOut['ssh_connection_data'] = false;

        if (count($_SESSION['ssh_connection_data'])) {
            $jsonOut['ssh_connection_data'] = $_SESSION['ssh_connection_data'];
            $jsonOut['status'] = true;
        }

        echo json_encode($jsonOut);
    }

    public function terminalcmd()
    {
        $ssh_data = $_SESSION['ssh_connection_data'];
        $post = $this->getParamsPost();

        error_log(print_r($post, 1));

        $jsonOut = [];
        try {

            $sshConnc = new \Fhc\SecurityShell\SshConnection(
                $ssh_data['user'], $ssh_data['host'], $ssh_data['fingerprint'], $ssh_data['port'], $ssh_data['password']
            );

            if (!empty($post['cmd'])) {
                $jsonOut['result'] = $sshConnc->exec($post['cmd']);
            } else {
                $jsonOut['result'] = null;
            }
            $jsonOut['msg'] = 'Sucesso';
            $jsonOut['status'] = true;
        } catch (\Exception $exc) {
            error_log("Erro de teste de conexao ssh({$exc->getMessage()})");
            $jsonOut['msg'] = 'Erro de teste de conexão ssh' .
                    $jsonOut['status'] = false;
        }

        echo json_encode($jsonOut);
    }

}
