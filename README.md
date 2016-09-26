# mt4
Teste Mt4

## Setup:


#### Git
Clona o projeto do Git

```sh
[/path/to/]$ git clone https://github.com/fernandohcorrea/mt4.git
```

#### Composer

Usar o composer para autoload do projeto

```sh
[/path/to/mt4]$ composer.phar update
```

#### Chmod e Chown

O apache/nginx tem de ter acesso de leitura e escrita em **database** e **filesystem**

```sh
[/path/to/mt4]$ sudo chown 666  database/ filesystem/ -R
[/path/to/mt4]$ sudo chown apache:apache  database/ filesystem/ -R
```

#### Apache v-host

O V-Hosta deve apontar para o public "/path/to/mt4/**public**"

```xml
#
# Virtual Hosts
#
<VirtualHost *:80>
    ServerName mt4
    ServerAdmin webmaster@mt4.example.com

    DocumentRoot "/path/to/mt4/public"

    ErrorLog "logs/mt4-error_log"
    CustomLog "logs/mt4-access_log" common

    <Directory "/path/to/mt4/public">
        AllowOverride ALL
        Options Indexes FollowSymLinks
    </Directory>

</VirtualHost>

```


*  **Entrada do Hosts**

Adicionar no /etc/hosts/ o domínio do v-host

```
[/path/to/mt4]$ sudo vim /etc/hosts

```

Arquivo /etc/hosts

```text
127.0.0.1   mt4
::1         localhost localhost.localdomain localhost6 localhost6.localdomain6

```

#### Extras

Caso necessário temos confgurações em config/mt4.ini


### SSH

São necessários  de trocar chaves de criptografia com o servidor para poder te acesso via ssh pela web.

Outra cois necessária é fingerprint de acesso ao servido.

as chaves devem ficar em **filesystem/ssh_keys/<user>/id_rsa...**

Use a plataforma de cloud de arquivo interna da aokicaçãom para criar as pastas e subir os arquivos.
