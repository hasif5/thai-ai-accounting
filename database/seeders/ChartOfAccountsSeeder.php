<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // 1000 - Assets
            ['code' => '1000', 'name' => 'สินทรัพย์', 'type' => 'asset', 'parent_id' => null],
            ['code' => '1100', 'name' => 'สินทรัพย์หมุนเวียน', 'type' => 'asset', 'parent_id' => '1000'],
            ['code' => '1110', 'name' => 'เงินสดและรายการเทียบเท่าเงินสด', 'type' => 'asset', 'parent_id' => '1100'],
            ['code' => '1120', 'name' => 'ลูกหนี้การค้า', 'type' => 'asset', 'parent_id' => '1100'],
            ['code' => '1130', 'name' => 'สินค้าคงเหลือ', 'type' => 'asset', 'parent_id' => '1100'],
            ['code' => '1200', 'name' => 'สินทรัพย์ไม่หมุนเวียน', 'type' => 'asset', 'parent_id' => '1000'],
            ['code' => '1210', 'name' => 'ที่ดิน อาคารและอุปกรณ์', 'type' => 'asset', 'parent_id' => '1200'],
            ['code' => '1220', 'name' => 'สินทรัพย์ไม่มีตัวตน', 'type' => 'asset', 'parent_id' => '1200'],
            
            // 2000 - Liabilities
            ['code' => '2000', 'name' => 'หนี้สิน', 'type' => 'liability', 'parent_id' => null],
            ['code' => '2100', 'name' => 'หนี้สินหมุนเวียน', 'type' => 'liability', 'parent_id' => '2000'],
            ['code' => '2110', 'name' => 'เจ้าหนี้การค้า', 'type' => 'liability', 'parent_id' => '2100'],
            ['code' => '2120', 'name' => 'เงินกู้ยืมระยะสั้น', 'type' => 'liability', 'parent_id' => '2100'],
            ['code' => '2200', 'name' => 'หนี้สินไม่หมุนเวียน', 'type' => 'liability', 'parent_id' => '2000'],
            ['code' => '2210', 'name' => 'เงินกู้ยืมระยะยาว', 'type' => 'liability', 'parent_id' => '2200'],
            
            // 3000 - Equity
            ['code' => '3000', 'name' => 'ส่วนของเจ้าของ', 'type' => 'equity', 'parent_id' => null],
            ['code' => '3100', 'name' => 'ทุนเรือนหุ้น', 'type' => 'equity', 'parent_id' => '3000'],
            ['code' => '3200', 'name' => 'กำไรสะสม', 'type' => 'equity', 'parent_id' => '3000'],
            
            // 4000 - Revenue
            ['code' => '4000', 'name' => 'รายได้', 'type' => 'revenue', 'parent_id' => null],
            ['code' => '4100', 'name' => 'รายได้จากการขาย', 'type' => 'revenue', 'parent_id' => '4000'],
            ['code' => '4200', 'name' => 'รายได้อื่น', 'type' => 'revenue', 'parent_id' => '4000'],
            
            // 5000 - Expenses
            ['code' => '5000', 'name' => 'ค่าใช้จ่าย', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5100', 'name' => 'ต้นทุนขาย', 'type' => 'expense', 'parent_id' => '5000'],
            ['code' => '5200', 'name' => 'ค่าใช้จ่ายในการขาย', 'type' => 'expense', 'parent_id' => '5000'],
            ['code' => '5300', 'name' => 'ค่าใช้จ่ายในการบริหาร', 'type' => 'expense', 'parent_id' => '5000'],
        ];

        foreach ($accounts as $account) {
            DB::table('chart_of_accounts')->insert($account);
        }
    }
}