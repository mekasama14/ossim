<?php
/**
* Policy_net_reference.inc
*
* File Policy_net_reference.inc is used to:
*   - Task 1 (TO DO)
*   - Task 2 (TO DO)
*   - Task 3 (TO DO)
*
*
* License:
*
* Copyright (c) 2003-2006 ossim.net
* Copyright (c) 2007-2013 AlienVault
* All rights reserved.
*
* This package is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 dated June, 1991.
* You may not use, modify or distribute this program under any other version
* of the GNU General Public License.
*
* This package is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this package; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
* MA  02110-1301  USA
*
*
* On Debian GNU/Linux systems, the complete text of the GNU General
* Public License can be found in `/usr/share/common-licenses/GPL-2'.
*
* Otherwise you can read it here: http://www.gnu.org/licenses/gpl-2.0.txt
*
* @package    ossim-framework\Policy&Actions
* @autor      AlienVault INC
* @license    http://www.gnu.org/licenses/gpl-2.0.txt
* @copyright  2003-2006 ossim.net
* @copyright  2007-2013 AlienVault
* @link       https://www.alienvault.com/
*/


class Policy_net_reference 
{
    public $policy_id;
    

    public $net_id;
    

    public $direction;
	

    public function __construct($policy_id, $net_id, $direction) 
    {
        $this->policy_id = $policy_id;
        $this->net_id    = $net_id;
        $this->direction = $direction;
    }
	
	
    public function get_policy_id() 
    {
        return $this->policy_id;
    }
	
	
    public function get_net_id() 
    {
        return $this->net_id;
    }
	
	
    public function get_direction() 
    {
        return $this->direction;
    }
    
	
    public static function get_list($conn, $policy_id, $direction)
    {
        $list   = array();

        $params = array($policy_id, $direction);
        $query  = "SELECT HEX(policy_id) AS policy_id, HEX(net_id) AS net_id, direction FROM policy_net_reference WHERE policy_id = UNHEX(?) AND direction = ?";
				
        $rs = $conn->Execute($query, $params);
		
        if (!$rs) 
        {
            Av_exception::write_log(Av_exception::DB_ERROR, $conn->ErrorMsg());
        } 
        else 
        {
            while (!$rs->EOF) 
            {
                $list[] = new self($rs->fields["policy_id"], $rs->fields["net_id"], $rs->fields["direction"]);
                
                $rs->MoveNext();
            }
        }
        
        return $list;        
    }
    
    
    public static function insert($conn, $net_list, $direction, $policy_id)
    {
        foreach($net_list as $net_id) 
        {
			$net_id = (!strcasecmp($net_id, 'any')) ? 0 : $net_id;
            
            $params = array($policy_id, $net_id, $direction);
            $query  = "INSERT INTO policy_net_reference VALUES (UNHEX(?), UNHEX(?), ?)";
                       
            if (!$conn->Execute($query, $params)) 
            {
                Av_exception::throw_error(Av_exception::DB_ERROR, $conn->ErrorMsg());
            }
        }        
    }
    
    
    public static function in_policy_net_reference($conn, $policy_id, $net_id, $direction) 
    {
        $params = array($policy_id, $net_id, $direction);
        $query  = "SELECT HEX(policy_id) AS policy_id, HEX(net_id) AS net_id, direction FROM policy_net_reference WHERE policy_id = UNHEX(?) and net_id = UNHEX(?) and direction = ?";
                
        $rs = $conn->Execute($query, $params);
        if (!$rs) 
        {
            Av_exception::throw_error(Av_exception::DB_ERROR, $conn->ErrorMsg());
        }
        
        return $rs->fields[0];        
    }
    
    
    public static function delete($conn, $policy_id)
    {
        $params = array($policy_id);
        $query  = "DELETE FROM policy_net_reference WHERE policy_id = UNHEX(?)";
                
        if ($conn->Execute($query, $params) === FALSE) 
        {
            Av_exception::throw_error(Av_exception::DB_ERROR, $conn->ErrorMsg());
        }        
    }
}

/* End of file policy_net_reference.inc */
/* Location: ../include/classes/policy_net_reference.inc */
