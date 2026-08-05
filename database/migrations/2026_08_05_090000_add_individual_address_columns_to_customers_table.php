<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'billing_address_line1')) {
                $table->string('billing_address_line1', 255)->nullable()->after('other_contact');
                $table->string('billing_address_line2', 255)->nullable()->after('billing_address_line1');
                $table->string('billing_city', 100)->nullable()->after('billing_address_line2');
                $table->string('billing_province', 100)->nullable()->after('billing_city');
                $table->string('billing_country', 100)->nullable()->default('Philippines')->after('billing_province');

                $table->string('shipping_address_line1', 255)->nullable()->after('billing_address');
                $table->string('shipping_address_line2', 255)->nullable()->after('shipping_address_line1');
                $table->string('shipping_city', 100)->nullable()->after('shipping_address_line2');
                $table->string('shipping_province', 100)->nullable()->after('shipping_city');
                $table->string('shipping_country', 100)->nullable()->default('Philippines')->after('shipping_province');
            }
        });

        // Helper to parse address strings
        $parseAddr = function($addrStr) {
            if (!$addrStr || $addrStr === 'N/A') {
                return ['', '', '', '', 'Philippines'];
            }
            if (str_contains($addrStr, '|')) {
                $p = array_map('trim', explode('|', $addrStr));
                return [$p[0] ?? '', $p[1] ?? '', $p[2] ?? '', $p[3] ?? '', $p[4] ?? 'Philippines'];
            }
            $parts = array_values(array_filter(array_map('trim', explode(',', $addrStr))));
            $len = count($parts);
            if ($len >= 5) {
                return [$parts[0], $parts[1], $parts[2], $parts[3], $parts[4]];
            } elseif ($len == 4) {
                return [$parts[0], '', $parts[1], $parts[2], $parts[3]];
            } elseif ($len == 3) {
                return [$parts[0], '', $parts[1], '', $parts[2]];
            } elseif ($len == 2) {
                return [$parts[0], '', $parts[1], '', 'Philippines'];
            }
            return [$parts[0] ?? '', '', '', '', 'Philippines'];
        };

        // Populate existing rows
        $customers = DB::table('customers')->get();
        foreach ($customers as $c) {
            list($b1, $b2, $bCity, $bProv, $bCoun) = $parseAddr($c->billing_address ?? '');
            list($s1, $s2, $sCity, $sProv, $sCoun) = $parseAddr($c->shipping_address ?? '');

            DB::table('customers')->where('customer_id', $c->customer_id)->update([
                'billing_address_line1' => $b1 ?: null,
                'billing_address_line2' => $b2 ?: null,
                'billing_city'          => $bCity ?: null,
                'billing_province'      => $bProv ?: null,
                'billing_country'       => $bCoun ?: 'Philippines',
                'shipping_address_line1' => $s1 ?: null,
                'shipping_address_line2' => $s2 ?: null,
                'shipping_city'          => $sCity ?: null,
                'shipping_province'      => $sProv ?: null,
                'shipping_country'       => $sCoun ?: 'Philippines',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'billing_address_line1',
                'billing_address_line2',
                'billing_city',
                'billing_province',
                'billing_country',
                'shipping_address_line1',
                'shipping_address_line2',
                'shipping_city',
                'shipping_province',
                'shipping_country',
            ]);
        });
    }
};
