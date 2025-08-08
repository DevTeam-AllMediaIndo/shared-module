<?php
namespace Allmedia\Shared\Regol\Core;

use Allmedia\Shared\Database\DatabaseInterface;
use Allmedia\Shared\Regol\Contracts\AccountInterface;
use Exception;

class Account implements AccountInterface {

    private $db;

    public function __construct(DatabaseInterface $db) {
        //Do your magic here
        $this->db = $db::connect();
    }

    public function accountDetail(string $idAcc): array|bool {
        try {
            $sqlGet = $this->db->query("
                SELECT 
                    tr.*,
                    tra.*,
                    tm.*
                FROM tb_racc tr 
                JOIN tb_member tm ON (tm.MBR_ID = tr.ACC_MBR)
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE)
                WHERE UPPER(tra.RTYPE_TYPE) != 'DEMO'
                AND MD5(MD5(tr.ID_ACC)) = '{$idAcc}'
                LIMIT 1
            ");

            return $sqlGet->fetc_assoc();

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function accountCondition(): array|bool {
        return [];
    }

    public function last(): array|bool {
        return [];
    }

    public function balance(): array|bool {
        return [];
    }
    
    public function suffix(): array|bool {
        return [];
    }

    public function commission(): array|bool {
        return [];
    }
    
}