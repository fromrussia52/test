<?php

namespace App\DB;

class Billing
{
    private $conn = null;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getBalans($userId)
    {
        $st = $this->conn->prepare('select value from billing where user_id=:userId');
        $st->bindParam('userId', $userId);
        $st->execute();
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        if ($res !== false) {
            return $res['value'];
        } else {
            return null;
        }
    }

    public function pullOff($value, $userId)
    {
        try {
            $this->conn->beginTransaction();
            //get current balans
            $balans = $this->getBalans($userId);
            if ((double) $balans === 0) {
                throw new \Exception('У Вас отсутствуют средства на балансе!');
            }
            if ((double) $balans < (double) $value) {
                throw new \Exception('Сумма для вывода превышает текущий баланс!');
            }
            $newBalans = (double) $balans - (double) $value;
            $st = $this->conn->prepare('update billing set value=:value where user_id=:userId');
            $st->bindParam('value', $newBalans);
            $st->bindParam('userId', $userId);
            $st->execute();
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
