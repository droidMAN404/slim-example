<?php

namespace Slime;

class Repository
{
    private static $id = 1000;
    private $filename = 'base/users';
    
    public function getFileName()
    {
        return $this->filename;
    }
    
    public function save ($user)
    {
        $newId = self::$id;
        self::$id += 1;
        $newUser = $user;
        $newUser['id'] = $newId;
        
        $file = $this->getFileName();
        if (file_exists($file)) {
            $users = unserialize(file_get_contents($file));
            $users[] = $user;
            file_put_contents($file, serialize($users));
        } else {
            $users = [$user];
            file_put_contents($file, serialize($users));
        }
    }

    public function get ()
    {
        $file = $this->getFileName();
        if (file_exists($file)) {
            $users = unserialize(file_get_contents($file));
            
            return $users;
        }
        
        return [];
    }
}