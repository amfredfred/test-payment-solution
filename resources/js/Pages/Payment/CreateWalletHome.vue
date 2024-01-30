<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';


defineProps<{
    currencies: { name: string, code: string }[]
    error: string,
    message: string
}>();


const form = useForm({
    initial_balance: '0',
    slug: '',
})



const submit = () => {
    form.post(route('post-store-user-wallet'), {
        onFinish: () => {
            form.reset('initial_balance');
        },
    });
};


const onSelect = (selected: any) => {
    console.log(selected, 'SELECTED')
}


</script>

<style>
.blance-container {
    padding-inline: 25px;
    padding-bottom: 20px;
    border-radius: 20px;
    width: 320px;
}

.form-container {
    padding: 10px;
    box-shadow: 0 0 1px inset black;
    background: white;
    border-radius: 5px;
    margin-top: 30px;
    max-width: 500px;
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
                    <div class="blance-container col-3">
                        d
                    </div>
                </div>

                <div class="form-container ">


                    <InputError class="mt-2" :message="$page.props.errors.default" />
                    <span v-if="$page.props.message" style="padding:20px; color: green;">
                        {{ $page.props.message }}
                    </span>

                    <form @submit.prevent="submit" style="display: flex;">

                        <div class="col-xl-6 auto">
                            <div class="row">
                                <div class="col-xl-4 mb-3">
                                    <InputLabel for="slug" value="Select Wallet Denomination" />
                                    <select @change="onSelect" name="slug" id="slug" v-model="form.slug">
                                        <option value="">Select</option>
                                        <option v-for="currency in currencies" :value="currency.code" :key="currency.code">
                                            {{ currency.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.slug" />
                                </div>

                                <div class="col-xl-4 mb-3">
                                    <InputLabel for="initial_balance" value="Initial balance" />
                                    <TextInput placeholder="'$0.00'" v-model="form.initial_balance" type="number"
                                        id="initial_balance" />
                                    <InputError class="mt-2" :message="form.errors.initial_balance" />
                                </div>
                                <div class="col-xl-12">
                                    <button class=""
                                        style="padding: 10px 40px; background: green; border-radius: 5px;color:white; margin-left:auto; width:100%">
                                        Create Wawllet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
