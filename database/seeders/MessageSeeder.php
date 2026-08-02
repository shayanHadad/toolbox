<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\Support\SeedContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    private array $rows = [];

    /** @var \Illuminate\Support\Collection<int, User[]> کارکنان هر شرکت (owner+adminها)، کلید = companyID */
    private $staffByCompany;

    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');

        $this->staffByCompany = User::whereIn('role', [3, 4])
            ->with('companyAdmin')
            ->get()
            ->filter(fn (User $u) => $u->companyAdmin?->companyID)
            ->groupBy(fn (User $u) => $u->companyAdmin->companyID);


        $this->seedOrderConversations($faker);
        $this->seedColdInquiries($faker);

        if (empty($this->rows)) {
            $this->command->warn('نه سفارشی هست نه کاربر کافی — پیامی ساخته نشد.');
            return;
        }

        foreach (array_chunk($this->rows, 500) as $chunk) {
            DB::table('messages')->insert($chunk);
        }

        $this->command->info('پیام‌ها ساخته شدند (' . count($this->rows) . ' مورد).');
    }

    private function seedOrderConversations($faker): void
    {
        $orders = Order::all();

        if ($orders->isEmpty()) {
            return;
        }

        foreach ($orders as $order) {
            if ($faker->boolean(20)) {
                continue;
            }

            $customer = User::find($order->customerID);

            if ($order->providerID) {
                $partnerPool = collect([User::find($order->providerID)])->filter();
                $companyID = null;
            } else {
                $partnerPool = $this->staffByCompany->get($order->companyID, collect());
                $companyID = $order->companyID;
            }

            if (! $customer || $partnerPool->isEmpty()) {
                continue;
            }

            $sequence = $this->buildSequence($faker, (int) $order->status, ! is_null($order->rating));

            $this->appendConversation($faker, $customer, $partnerPool, $companyID, $sequence);
        }
    }

    private function seedColdInquiries($faker): void
    {
        $customers = User::where('role', 1)->get();
        $experts   = User::where('role', 2)->whereHas('expertDetail')->get();
        $companies = Company::all();

        if ($customers->isEmpty() || ($experts->isEmpty() && $companies->isEmpty())) {
            return;
        }

        for ($i = 0; $i < 40; $i++) {
            $customer = $customers->random();

            $goesToExpert = $experts->isNotEmpty() && $faker->boolean(60);

            if ($goesToExpert) {
                $partnerPool = collect([$experts->random()]);
                $companyID = null;
            } else {
                if ($companies->isEmpty()) {
                    continue;
                }

                $company = $companies->random();
                $partnerPool = $this->staffByCompany->get($company->companyID, collect());
                $companyID = $company->companyID;
            }

            if ($partnerPool->isEmpty() || $partnerPool->contains('userID', $customer->userID)) {
                continue;
            }

            $sequence = [['customer', 'opener']];

            if ($faker->boolean(65)) {
                $sequence[] = ['partner', 'reply'];
            }
            if ($faker->boolean(30)) {
                $sequence[] = ['customer', 'followup'];
            }

            $this->appendConversation($faker, $customer, $partnerPool, $companyID, $sequence);
        }
    }

    private function buildSequence($faker, int $status, bool $wasReviewed): array
    {
        return match ($status) {
            Order::STATUS_WAITING => array_values(array_filter([
                ['customer', 'opener'],
                $faker->boolean(60) ? ['partner', 'reply'] : null,
            ])),

            Order::STATUS_IN_PROGRESS => array_values(array_filter([
                ['customer', 'opener'],
                ['partner', 'reply'],
                $faker->boolean(70) ? ['customer', 'followup'] : null,
                $faker->boolean(70) ? ['partner', 'followupReply'] : null,
            ])),

            Order::STATUS_FINISHED => array_values(array_filter([
                ['customer', 'opener'],
                ['partner', 'reply'],
                ['customer', 'followup'],
                ['partner', 'followupReply'],
                $wasReviewed ? ['customer', 'thanks'] : null,
                $wasReviewed ? ['partner', 'thanksReply'] : null,
            ])),

            Order::STATUS_REJECTED => [
                ['customer', 'opener'],
                ['partner', 'decline'],
            ],

            Order::STATUS_CANCELLED => $faker->boolean(50)
                ? [['customer', 'opener']]
                : [],

            default => [],
        };
    }

    private function appendConversation($faker, User $customer, $partnerPool, ?int $companyID, array $sequence): void
    {
        if (empty($sequence)) {
            return;
        }

        $textPools = [
            'opener'         => SeedContent::chatOpeners(),
            'reply'          => SeedContent::chatReplies(),
            'decline'        => SeedContent::chatDeclines(),
            'followup'       => SeedContent::chatFollowups(),
            'followupReply'  => SeedContent::chatFollowupReplies(),
            'thanks'         => SeedContent::chatThanks(),
            'thanksReply'    => SeedContent::chatThanksReplies(),
        ];

        $timestamp = Carbon::now()
            ->subDays($faker->numberBetween(1, 150))
            ->subHours($faker->numberBetween(0, 23))
            ->subMinutes($faker->numberBetween(0, 59));

        $lastIndex = count($sequence) - 1;

        foreach ($sequence as $index => [$role, $type]) {
            $partner = $partnerPool->count() > 1 ? $partnerPool->random() : $partnerPool->first();

            $sender   = $role === 'customer' ? $customer : $partner;
            $receiver = $role === 'customer' ? $partner : $customer;

            $timestamp = $timestamp->copy()->addMinutes($faker->numberBetween(15, 600));

            $status = ($index === $lastIndex && $faker->boolean(35)) ? 0 : 1;

            if ($faker->boolean(3)) {
                $status = -1;
            }

            $this->rows[] = [
                'senderID'   => $sender->userID,
                'receiverID' => $receiver->userID,
                'message'    => $faker->randomElement($textPools[$type]),
                'created_at' => $timestamp->format('Y-m-d H:i:s'),
                'status'     => $status,
                'companyID'  => $companyID,
            ];
        }
    }
}
