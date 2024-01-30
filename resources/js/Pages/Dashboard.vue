<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';



defineProps<{
    wallets: { balance: number, slug: string, name: string, transsaction_count: number }[];
    user: User
}>();

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white bg-danger overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="row" style="padding: 20px; display: flex; gap: 10px;align-items: center;">
                        <a style="padding: 10px 20px; background: green; border-radius: 50px;"
                            :href="route('show-transfer-page')">Transfer</a>
                        <a style="padding: 10px 20px; background: orangered; border-radius: 50px;"
                            :href="route('show-create-wallet-page')">Create Wallet</a>
                    </div>

                    <div class="col-xl-12 " style="padding: 20px; display: flex; gap: 10px;align-items: center;">
                        <div class="row ">
                            <div class="col-xl-4" v-for="wallet in wallets" style="box-shadow: 0 0 2px black inset;background: whitesmoke;padding: 10px 20px;border-radius: 5px;">
                                {{ wallet['balance'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
