<?php
namespace pointybeard\Kickstarter\ExportParser\Tests\Seeders;

class generateValidData
{
    private static $faker;

    public function __construct()
    {
        self::$faker = \Faker\Factory::create('en_AU');
    }

    public function createValidCSVArchive($path, $totalRowsWithSurvey=50, $totalRowsWithoutSurvey=50)
    {
        $zip = new \ZipArchive;
        $res = $zip->open($path, \ZipArchive::CREATE);
        if ($res !== true) {
            throw new \Exception('Failed to open zip archive.');
        }

        if ($totalRowsWithSurvey > 0) {
            $zip->addFile(
                $this->testGenerateDataForTestsWithSurvey($totalRowsWithSurvey),
                'Kickstarter Backer Report - $59 - Aug 18 07am.csv'
            );
        }

        if ($totalRowsWithoutSurvey > 0) {
            $zip->addFile(
                $this->testGenerateDataForTestsNoSurvey($totalRowsWithoutSurvey),
                'Kickstarter Backer Report - No reward - Aug 18 07am.csv'
            );
        }

        $zip->close();

        return true;
    }

    public function testGenerateDataForTestsWithSurvey($total=50)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'ks_parser');
        $fp = fopen($tmpFile, 'w');

        fputcsv($fp, ["Backer Number", "Backer UID", "Backer Name", "Email", "Shipping Country", "Shipping Amount", "Reward Minimum", "Pledge Amount", "Pledged At", "Rewards Sent?", "Pledged Status", "Notes", "Billing State/Province", "Billing Country", "Survey Response", "Shipping Name", "Shipping Address 1", "Shipping Address 2", "Shipping City", "Shipping State", "Shipping Postal Code", "Shipping Country Name", "Shipping Country Code", "What Is Your Contact Phone Number?", "Add On: How Many Additional Copies Did You Pledge For?", "What Is Your Region/Continent?"]);

        for ($ii = 0; $ii < $total; $ii++) {
            fputcsv($fp, [
                "Backer Number" => self::$faker->numberBetween(1, 9999),
                "Backer UID" => self::$faker->randomNumber(9),
                "Backer Name" => self::$faker->name(),
                "Email" => self::$faker->safeEmail(),
                "Shipping Country" =>  self::$faker->randomElement(['AU', 'US', 'UK', 'EU']),
                "Shipping Amount" => "$" . self::$faker->randomFloat(2, 1, 100) . " AUD",
                "Reward Minimum" => "$" . self::$faker->randomFloat(2, 1, 100) . " AUD",
                "Pledge Amount" => "$" . self::$faker->randomFloat(2, 1, 100) . " AUD",
                "Pledged At" => self::$faker->date('Y-m-d') . ", " . self::$faker->time('H:i'),
                "Rewards Sent?" => self::$faker->randomElement(['', 'Yes', 'No']),
                "Pledged Status" => self::$faker->randomElement(['collected', 'error']),
                "Notes" => self::$faker->realText(50),
                "Billing State/Province" => self::$faker->stateAbbr(),
                "Billing Country" => self::$faker->randomElement(['AU', 'US', 'UK', 'EU']),
                "Survey Response" => self::$faker->date('Y-m-d') . ", " . self::$faker->time('H:i'),
                "Shipping Name" => self::$faker->name(),
                "Shipping Address 1" => self::$faker->address(),
                "Shipping Address 2" => self::$faker->secondaryAddress(),
                "Shipping City" => self::$faker->city(),
                "Shipping State" =>  self::$faker->stateAbbr(),
                "Shipping Postal Code" => self::$faker->postcode(),
                "Shipping Country Name" => self::$faker->country(),
                "Shipping Country Code" => self::$faker->randomElement(['AU', 'US', 'UK', 'EU']),
                "What Is Your Contact Phone Number?" => self::$faker->phoneNumber(),
                "Add On: How Many Additional Copies Did You Pledge For?" => self::$faker->numberBetween(0, 4),
                "What Is Your Region/Continent?" => self::$faker->randomElement(['United States', 'Europe', 'Asia/Oceania', 'Other'])
            ]);
        }

        fclose($fp);
        return $tmpFile;
    }

    public function testGenerateDataForTestsNoSurvey($total=50)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'ks_parser');
        $fp = fopen($tmpFile, 'w');

        fputcsv($fp, ["Backer Number","Backer UID","Backer Name","Email","Pledge Amount","Pledged At","Pledged Status","Notes","Billing State/Province","Billing Country"]);

        for ($ii = 0; $ii < $total; $ii++) {
            fputcsv($fp, [
                "Backer Number" => self::$faker->numberBetween(1, 9999),
                "Backer UID" => self::$faker->randomNumber(9),
                "Backer Name" => self::$faker->name(),
                "Email" => self::$faker->safeEmail(),
                "Pledge Amount" => "$" . self::$faker->randomFloat(2, 1, 100),
                "Pledged At" => self::$faker->date('Y-m-d') . ", " . self::$faker->time('H:i'),
                "Pledged Status" => self::$faker->randomElement(['collected', 'error']),
                "Notes" => self::$faker->realText(50),
                "Billing State/Province" => self::$faker->stateAbbr(),
                "Billing Country" => self::$faker->randomElement(['AU', 'US', 'UK', 'EU'])
            ]);
        }

        fclose($fp);
        return $tmpFile;
    }
}
