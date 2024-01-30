<template>
    <vue-qrcode value="https://www.1stg.me" @change="onDataUrlChange" />
</template>
<script>
import VueQrcode from 'vue-qrcode'

export default {
    data() {
        return {
            dataUrl: null,
        }
    },
    components: {
        VueQrcode,
    },
    methods: {
        onDataUrlChange(dataUrl) {
            this.dataUrl = dataUrl
        },
    },
}
</script>