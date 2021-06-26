<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'password', 'avatar', 'email', 'phone', 'last_activity', 'created_at', 'updated_at', 'verified_email', 'verified_phone'
    ];
    protected $aliasName = ['users' => 'a', 'address' => 'c', 'data_customers' => 'b'];
    protected $returnType    = 'App\Entities\User';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'full_name'     => 'required|min_length[3]',
        'username'     => 'required|alpha_numeric_space|min_length[3]',
        'email'        => 'required|valid_email'
    ];
    // protected $validationMessages = [];
    protected $skipValidation     = false;

    public function search($query, $limit = 10, $offset = 0, array $filters)
    {

        $this->builder()->select('
        a.id, a.avatar, a.username, a.email, a.phone, a.last_activity, a.created_at,
        b.first_name, b.last_name,
        c.province, c.city, c.subdistrict, c.street')->from('users a')
            ->join('data_customers b', 'b.user_id=a.id')
            ->join('address c', 'c.user_id=a.id AND c.primary=1')
            ->like('a.username', $query);
        if ($filters) {
            $this->sort($filters)->where($filters);
        }
        return $this->builder->groupBy('a.id')->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function count($query = null, $filters = null)
    {
        $this->builder()->select('a.id, c.id as address_id')->from('users a')
            ->join('data_customers b', 'b.user_id=a.id')
            ->join('address c', 'c.user_id=a.id AND c.primary=1');

        if ($filters) {
            $this->where($filters);
        }
        if ($query) {
            $this->builder->like('a.username', $query);
        }
        $result = $this->builder->groupBy('a.id')->get()->getResultArray();
        return $result == null ? 0 : (int) count($result);
    }

    public function users($limit = 10, $offset = 0, array $filters)
    {
        $this->builder()->select('
        a.id, a.avatar, a.username, a.email, a.phone, a.last_activity, a.created_at,
        b.first_name, b.last_name,
        c.province, c.city, c.subdistrict, c.street')->from('users a')
            ->join('data_customers b', 'b.user_id=a.id')
            ->join('address c', 'c.user_id=a.id AND c.primary=1')
            ->groupBy('a.id');
        if ($filters) {
            $this->sort($filters)->where($filters);
        }
        return $this->builder->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    protected function where($filters)
    {

        helper('my_helper');
        $wheres = [];
        if (isset($filters['address']) && !empty($filters['address'])) {
            $wheres['c.subdistrict'] = $filters['address'];
        }

        $noWhere = ['sort','address'];
        foreach ($filters as $key => $value) {
            if (!isKey($key, $noWhere) && $value) {
                $wheres[$key] = $value;
            }
        }
        $this->builder->where($wheres);
        return $this;
    }

    protected function sort($filters)
    {
        if (isset($filters['sort']) && !empty($filters['sort'])) {
            $ex = explode('.', $filters['sort']);
            $isAlias = dot_array_search($ex[0], $this->aliasName);
            if ($isAlias && count($ex) === 3) {
                $this->builder->orderBy($this->aliasName[$ex[0]] . '.' . $ex[1], $ex[2]);
            } else {
                $this->builder->orderBy('a.' . $ex[0], $ex[1]);
            }
        }
        return $this;
    }

    public function user($id, $isDataCustomer = FALSE)
    {
        $where = is_numeric($id) ? 'a.id' : 'a.username';
        if ($isDataCustomer) {
            return $this->builder()->select('a.*, 
            b.first_name, b.last_name, b.gender, b.date_of_birth,
             c.role_id as role')->from('users a')
                ->join('data_customers b', 'b.user_id=a.id', 'left')
                ->join('user_roles c', 'c.user_id=a.id', 'left')
                ->where($where, $id)->get()->getRow(0, $this->returnType);
        } else {
            return $this->builder()->select('a.*, b.role_id as role')->from('users a')
                ->join('user_roles b', 'b.user_id=a.id', 'left')
                ->where($where, $id)->get()->getRow(0, $this->returnType);
        }
    }

    public function update($id = null, $data = null): bool
    {
        $newData = [];
        $result = false;
        helper('array_helper');
        foreach ($this->allowedFields as $key) {
            $search = dot_array_search($key, $data);
            if ($search != null) {
                $newData[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        if (count($data) > 0) {
            $result = $this->db->table('data_customers')->update($data, ['user_id' => $id]);
        }

        if (count($newData) > 0) {
            return parent::update($id, $newData);
        } else {
            return $result;
        }
    }

    public function find($id = null)
    {
        return $this->user($id, true);
    }

    public function delete($id = null, bool $purge = false)
    {
        parent::delete($id, $purge);
        $this->db->table('data_customers')->delete(['user_id' => $id]);
        $this->db->table('logins')->delete(['user_id' => $id]);
    }

    public function logins($uid)
    {
        $query = $this->db->table('logins')->select()->where('user_id', $uid)->get()->getResultArray();
        return $query;
    }

    public function last_login($uid)
    {
        $query = $this->db->table('logins')->select()
            ->where('user_id', $uid)->orderBy('time', 'asc')->get()->getLastRow();
        return $query;
    }

    public function update_activity($uid)
    {
        $this->builder()->update(['last_activity' => time()], ['id' => $uid]);
    }

    public function update_login($uid, $data = null)
    {
        $logins = $this->logins($data['user_id']);
        foreach ($logins as $value) {
            if ($data['device_token'] === $value['device_token'] || $value['login'] === $data['login']) {
                $uid = $data['user_id'];
                unset($data['user_id']);
                return $this->db->table('logins')->update($data, ['user_id' => $uid, 'id' => $value['id']]);
            }
        }
        return $this->db->table('logins')->insert($data);
    }

    public function logout($uid, $loginId)
    {
        return $this->db->table('logins')->update(['device_token' => ''], ['user_id' => $uid, 'id' => $loginId]);
    }

    public function role($uid)
    {
        $query = $this->db->table('user_roles')->select()->where('user_id', $uid)->get()->getRow();
        return $query;
    }

    public function register($data, $dataCustomer, $role)
    {
        $this->db->transStart();
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $this->db->table($this->table)->insert($data);
        $userId =  $this->db->insertID();
        $dataCustomer['user_id'] = $userId;

        $this->db->table('data_customers')->insert($dataCustomer);
        $this->db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => $role]);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return null;
        } else {
            return $userId;
        }
    }
}
