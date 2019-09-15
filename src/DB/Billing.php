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
        //get transfer state
        $state = $this->getTransferState($userId);
        if ((int)$state === 1) { 
            throw new \Exception('Операция снятия еще не закончилась');
        }

        $this->conn->beginTransaction();
        try {
            //set trans active
            $this->setTransferState(1, $userId);

            //get current balans
            $balans = $this->getBalans($userId);
            if (bccomp((string) $balans, '0') === 0) {
                throw new \Exception('У Вас отсутствуют средства на балансе!');
            }

            $newBalans = (float) $balans - (float) $value;
            if (bccomp((string) $newBalans, '0') === -1) {
                throw new \Exception('Сумма для вывода превышает текущий баланс!');
            }

            $st = $this->conn->prepare('update billing set value=:value where user_id=:userId');
            $st->bindParam('value', $newBalans);
            $st->bindParam('userId', $userId);
            $st->execute();

            //set trans unactive
            $this->setTransferState(0, $userId);

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();

            //set trans unactive
            $this->setTransferState(0, $userId);

            throw new \Exception($e->getMessage());
        }
    }

    private function setTransferState($state, $userId)
    {
        $st = $this->conn->prepare('update billing set is_trans_active=:state where user_id=:userId');
        $st->bindParam('state', $state);
        $st->bindParam('userId', $userId);
        $st->execute();
    }

    private function getTransferState($userId)
    {
        $st = $this->conn->prepare('select is_trans_active from billing where user_id=:userId');
        $st->bindParam('userId', $userId);
        $st->execute();
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        if ($res !== false) {
            return $res['is_trans_active'];
        } else {
            throw new \Exception('Балан не найден');
        }
    }
}
