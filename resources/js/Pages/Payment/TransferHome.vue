<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    amount: '',
    recipient_id: '',
    wallet_slug: ''
})



defineProps<{
    wallets: { balance: number, slug: string, name: string, transsaction_count: number }[];
    users: { name: string, id: number | string }[]
    recipient_id: number
}>();


const submit = () => {
    form.post(route('post-transfer-funds'), {
        onFinish: () => {
            form.reset('amount');
        },
    });
};

</script>

<style>
.blance-container {
    padding-inline: 25px;
    padding-bottom: 20px;
    border-radius: 20px;
    width: 320px;
}

.form-container {
    width: 100%;
    padding: 10px;
    box-shadow: 0 0 1px inset black;
    background: white;
    border-radius: 5px;
    margin-top: 30px;
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Transfer To User</div>
                </div>
                <div class="form-container">
                    <InputError class="mt-2" :message="$page.props.errors.default" />

                    <form @submit.prevent="submit" :style="'display:flex; flex-wrap:wrap; gap:10px'">
                        <div class="row"
                            style="display: flex;align-items: flex-end;justify-content: space-between; gap: 10px;">
                            <div class="col-xl-4" style="display: flex;flex-direction: column;">
                                <InputError :message="$page.props.errors.amount" />
                                <TextInput placeholder="0.00" type="number" v-model="form.amount" />
                            </div>
                            <div class="col-xl-4" style="display: flex;flex-direction: column;">
                                <InputError :message="$page.props.errors.wallet_slug" />
                                <select v-model="form.wallet_slug">
                                    <option value="" selected>Choose Wallet</option>
                                    <option v-for=" wallet  in  wallets " :value="wallet.slug">
                                        {{ wallet.slug.concat(' ' + wallet.balance as any) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-xl-4" style="display: flex;flex-direction: column;">
                                <InputError :message="$page.props.errors.recipient_id" />
                                <span v-if="recipient_id" style="margin: auto;">
                                    send to <strong style="color: green;">{{ users[recipient_id] }}</strong>
                                </span>
                                <select v-if="!recipient_id" v-model="form.recipient_id">
                                    <option value='' selected>Choose Recipeint</option>
                                    <option v-for=" (name, index)  in  users " :value="index">{{ name }}</option>
                                </select>
                            </div>
                        </div>

                        <button :class="''"
                            :style="'padding: 10px 40px; background: green; border-radius: 50px;color:white; margin-left:auto'">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
