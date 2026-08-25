<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(UserDetailsTableSeeder::class);
        // $this->call(AdminSupersTableSeeder::class);
        // $this->call(AdminUsersTableSeeder::class);
        $this->call(OperatorsTableSeeder::class);
        $this->call(SenderIdVirtualNumbersTableSeeder::class);
        $this->call(SenderIdRegistersTableSeeder::class);
        $this->call(SenderIdUsersTableSeeder::class);
        $this->call(SenderIdNonMaskingsTableSeeder::class);
        $this->call(SenderIdUserDefaultsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(AccSmsRatesTableSeeder::class);
        $this->call(AccPayMethodsTableSeeder::class);
        $this->call(AccSmsBalancesTableSeeder::class);
        $this->call(PhonebookCampaignCategoriesTableSeeder::class);
        $this->call(PhonebookCampaignContactsTableSeeder::class);
        $this->call(SmsTemplatesTableSeeder::class);
        $this->call(PhonebookCategoriesTableSeeder::class);
        $this->call(PhonebookContactsTableSeeder::class);
        $this->call(AccUserCreditHistoriesTableSeeder::class);
        $this->call(SmsCampaignIdsTableSeeder::class);
        $this->call(SmsCamPendingsTableSeeder::class);
        $this->call(SmsCampaign24hsTableSeeder::class);
        $this->call(SmsCampaignsTableSeeder::class);
        $this->call(EmployeeUsersTableSeeder::class);
    }
}
