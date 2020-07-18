# PFSENSE-CONFIG

Este site permite que o usuário se autentique com senha única e altere a liberação do NAT para seu ip atual.

Ele procura por regras que contenham o codpes do usuário e atualiza o source ip para refletir o ip atual.

## Configuração

* clonar o repositório
* rodar `composer install`
* copiar `config.sample.php` para `config.php`  e configurar
* O servidor que hospeda o sistema deve ter acesso ssh ao firewall pfsense
* O acesso ao pfsense deve ser com chave
* No pfsense deve ser colocado o script pfsense/updateNat em /etc/phpshellsessions/
* as Regras de nat no pfsense devem conter o codpes do usuário para serem encontrados
* O usuário pode ter mais de uma regra de NAT que todos serão atualizados
