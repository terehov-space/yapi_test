<template>
    <v-app>
        <v-main>
            <v-container fluid>
                <v-item-group selected-class="bg-primary" multiple v-model="deviceList">
                    <v-container>
                        <v-row>
                            <v-col
                                v-for="device in devices"
                                :key="device.id"
                                cols="12"
                                md="4"
                            >
                                <v-item v-slot="{ selectedClass, toggle }">
                                    <v-card
                                        :class="['d-flex align-center', selectedClass]"
                                        dark
                                        height="200"
                                        @click="toggle"
                                    >
                                        <div
                                            class="text-h3 flex-grow-1 text-center"
                                        >
                                            {{ device.name }}
                                        </div>
                                    </v-card>
                                </v-item>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-item-group>
                <v-row class="d-flex justify-start align-start">
                    <v-col class="d-flex justify-center" cols="12">
                        <v-color-picker
                            v-model="color"
                            mode="rgba"
                        ></v-color-picker>
                    </v-col>
                </v-row>
                <v-row class="d-flex justify-start align-start">
                    <v-col class="d-flex justify-center" cols="12">
                        <v-btn @click="appendColor">Применить</v-btn>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import axios from 'axios'

export default {
    name: "Light",
    props: ['devices', 'needAuth'],
    data: () => ({
        color: {r: 255, g: 255, b: 255},
        deviceList: [],
    }),
    methods: {
        appendColor() {
            const selectedDevices = this.devices.filter((item, i) => this.deviceList.includes(i))
            axios.post('/light', {
                devices: selectedDevices,
                color: this.color,
            })
        },
    },
}
</script>

<style scoped>

</style>
