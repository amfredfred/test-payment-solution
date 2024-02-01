<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import QrCode from '@/Components/QrCode.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    wallets: { balance: number, slug: string, name: string, transsaction_count: number }[];
    user: User
    pay_link: string
}>();


</script>

<style>
.qr-code-container {
    width: 200px;
    aspect-ratio: 1;
    background: red;
    display: flex;
    padding: 5px;
    flex-direction: column;
}
</style>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white bg-danger overflow-hidden shadow-sm sm:rounded-lg" style="display: flex;">
                    <div class="row" style="padding: 20px; display: flex; gap: 10px; flex-direction: column; flex-grow: 1;">
                        <div style="display: flex;flex-direction: row;align-items: center;  gap: 10px;">
                            <a style="padding: 10px 20px; background: green; border-radius: 50px;"
                                :href="route('show-transfer-page')">Transfer</a>
                            <a style="padding: 10px 20px; background: orangered; border-radius: 50px;"
                                :href="route('show-create-wallet-page')">Create Wallet</a>

                            <span>{{ pay_link }}</span>
                    </div>

                        <div class=" col-xl-12" style="  display: flex; gap: 10px;align-items: center;width: 100%;">
                            <div class="col-xl-4" v-for="wallet in wallets"
                                style="box-shadow: 0 0 2px black inset;background: whitesmoke;padding: 10px 20px;border-radius: 5px;">
                                {{ wallet['slug'].concat(' ' + wallet['balance'] as any) }}
                            </div>
                        </div>
                    </div>

                    <div class="qr-code-container">
                        <InputLabel value="Scan to Pay"
                            style="color: white; font-size: 24px; padding: 4px; text-transform: uppercase;" />
                        <QrCode :value="pay_link" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
