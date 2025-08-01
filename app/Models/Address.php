<?php

namespace App\Models;

use App\Core\Model;

class Address extends Model
{
    protected $table = 'user_addresses';
    protected $fillable = [
        'user_id', 'type', 'name', 'phone', 'address_line_1', 
        'address_line_2', 'city', 'state', 'postal_code', 
        'country', 'is_default'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get user's addresses
     */
    public function getUserAddresses($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
        return $this->db->query($sql, [$userId])->fetchAll();
    }

    /**
     * Get user's default address
     */
    public function getDefaultAddress($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND is_default = 1 LIMIT 1";
        return $this->db->query($sql, [$userId])->fetch();
    }

    /**
     * Get address by type
     */
    public function getAddressByType($userId, $type)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND type = ? ORDER BY is_default DESC LIMIT 1";
        return $this->db->query($sql, [$userId, $type])->fetch();
    }

    /**
     * Create new address
     */
    public function createAddress($data)
    {
        // If this is set as default, remove default from other addresses
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->removeDefaultStatus($data['user_id']);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }

    /**
     * Update address
     */
    public function updateAddress($addressId, $data)
    {
        // If this is set as default, remove default from other addresses
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $address = $this->find($addressId);
            if ($address) {
                $this->removeDefaultStatus($address['user_id']);
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($addressId, $data);
    }

    /**
     * Remove default status from all user addresses
     */
    private function removeDefaultStatus($userId)
    {
        $sql = "UPDATE {$this->table} SET is_default = 0 WHERE user_id = ?";
        return $this->db->query($sql, [$userId]);
    }

    /**
     * Set address as default
     */
    public function setAsDefault($addressId, $userId)
    {
        // Remove default from all addresses
        $this->removeDefaultStatus($userId);
        
        // Set this address as default
        $data = ['is_default' => 1, 'updated_at' => date('Y-m-d H:i:s')];
        return $this->update($addressId, $data);
    }

    /**
     * Delete address
     */
    public function deleteAddress($addressId, $userId)
    {
        // Check if this is the user's address
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND user_id = ?";
        $address = $this->db->query($sql, [$addressId, $userId])->fetch();

        if (!$address) {
            return false;
        }

        // If this was the default address, set another as default
        if ($address['is_default'] == 1) {
            $sql = "UPDATE {$this->table} SET is_default = 1 WHERE user_id = ? AND id != ? ORDER BY created_at DESC LIMIT 1";
            $this->db->query($sql, [$userId, $addressId]);
        }

        return $this->delete($addressId);
    }

    /**
     * Get formatted address string
     */
    public function getFormattedAddress($addressId)
    {
        $address = $this->find($addressId);
        if (!$address) return '';

        $formatted = ($address['first_name'] . ' ' . $address['last_name']) . "\n";
        $formatted .= $address['phone'] . "\n";
        $formatted .= $address['address_line_1'] . "\n";
        
        if (!empty($address['address_line_2'])) {
            $formatted .= $address['address_line_2'] . "\n";
        }
        
        $formatted .= $address['city'] . ", " . $address['state'] . " " . $address['postal_code'] . "\n";
        $formatted .= $address['country'];

        return $formatted;
    }

    /**
     * Validate Pakistan postal code
     */
    public function validatePakistanPostalCode($postalCode)
    {
        // Pakistan postal codes are 5 digits
        return preg_match('/^\d{5}$/', $postalCode);
    }

    /**
     * Get Pakistan cities list
     */
    public function getPakistanCities()
    {
        return [
            'Karachi', 'Lahore', 'Faisalabad', 'Rawalpindi', 'Gujranwala',
            'Peshawar', 'Multan', 'Hyderabad', 'Islamabad', 'Quetta',
            'Bahawalpur', 'Sargodha', 'Sialkot', 'Sukkur', 'Larkana',
            'Sheikhupura', 'Jhang', 'Rahim Yar Khan', 'Gujrat', 'Kasur',
            'Mardan', 'Mingora', 'Dera Ghazi Khan', 'Sahiwal', 'Nawabshah',
            'Okara', 'Mirpur Khas', 'Chiniot', 'Kamoke', 'Mandi Bahauddin',
            'Jhelum', 'Sadiqabad', 'Jacobabad', 'Shikarpur', 'Khanewal',
            'Hafizabad', 'Kohat', 'Muzaffargarh', 'Khanpur', 'Gojra',
            'Attock', 'Vehari', 'Wah Cantonment', 'Kasur', 'Pakpattan',
            'Talagang', 'Daska', 'Gujranwala', 'Malakwal', 'Taxila'
        ];
    }

    /**
     * Get Pakistan states/provinces
     */
    public function getPakistanStates()
    {
        return [
            'Punjab',
            'Sindh', 
            'Khyber Pakhtunkhwa',
            'Balochistan',
            'Gilgit-Baltistan',
            'Azad Kashmir',
            'Islamabad Capital Territory'
        ];
    }

    /**
     * Validate phone number for Pakistan
     */
    public function validatePakistanPhone($phone)
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);
        
        // Check if it's a valid Pakistani number
        if (preg_match('/^(92|0)?[0-9]{10}$/', $phone)) {
            return true;
        }
        
        return false;
    }

    /**
     * Format phone number for Pakistan
     */
    public function formatPakistanPhone($phone)
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);
        
        // Add country code if not present
        if (substr($phone, 0, 2) !== '92') {
            if (substr($phone, 0, 1) === '0') {
                $phone = '92' . substr($phone, 1);
            } else {
                $phone = '92' . $phone;
            }
        }
        
        return '+' . $phone;
    }

    /**
     * Get address count for user
     */
    public function getUserAddressCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $result = $this->db->query($sql, [$userId])->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Check if user has default address
     */
    public function hasDefaultAddress($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_default = 1";
        $result = $this->db->query($sql, [$userId])->fetch();
        return ($result['count'] ?? 0) > 0;
    }
}
