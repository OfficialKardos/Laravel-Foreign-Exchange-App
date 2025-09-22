<template>
    <div class="container">
        <h1 class="center">Currency Converter</h1>

        <input
            id="foreignAmount"
            type="number"
            class="form-control"
            placeholder="Enter foreign amount"
            v-model.number="foreignAmount"
            @input="calculateZAR"
            min="0"
        />

        <div class="form-group">
            <label for="currencySelect">Select Currency:</label>
            <select
                id="currencySelect"
                class="form-control"
                v-model="selectedCurrencyCode"
                :disabled="loadingCurrencies"
            >
                <option value="">Select Currency</option>
                <option v-if="loadingCurrencies" disabled>
                    Loading currencies...
                </option>
                <option
                    v-for="currency in currencies"
                    :key="currency.id"
                    :value="currency.code"
                >
                    {{ currency.name }} ({{ currency.code }})
                </option>
            </select>
        </div>
        <div class="form-group surcharge">
            <p>
                Surcharge: <span>{{ surcharge }}%</span>
            </p>
        </div>
        <input
            type="number"
            id="zarAmount"
            class="form-control"
            placeholder="or Enter ZAR Amount"
            v-model.number="zarAmount"
            @input="calculateForeign"
            min="0"
        />

        <button class="btn btn-primary" @click="submitOrder">
            Place Order
        </button>

        <div v-if="loading" id="overlay">
            <div class="loader"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const currencies = ref([]);
const selectedCurrencyCode = ref("");
const foreignAmount = ref(null);
const zarAmount = ref(null);
const surcharge = ref(0);
const loading = ref(false);
const loadingCurrencies = ref(false);

watch(selectedCurrencyCode, () => {
    if (selectedCurrencyCode.value) {
        if (foreignAmount.value !== null && zarAmount.value === null) {
            calculateZAR();
        } else if (zarAmount.value !== null && foreignAmount.value === null) {
            calculateForeign();
        } else if (
            (foreignAmount.value === null && zarAmount.value === null) ||
            (foreignAmount.value !== null && zarAmount.value !== null)
        ) {
            Swal.fire(
                "Error",
                "Please select either Foreign amount or ZAR amount to calculate.",
                "error"
            );
        }
    } else {
        surcharge.value = 0;
    }
});

function calculateZAR() {
    try {
        const currency = currencies.value.find(
            (c) => c.code === selectedCurrencyCode.value
        );
        if (!currency || !foreignAmount.value) {
            zarAmount.value = null;
            surcharge.value = 0;
            return;
        }

        const rate = parseFloat(currency.exchange_rate);
        const surchargePercent = parseFloat(currency.surcharge_percentage);

        if (isNaN(rate) || isNaN(surchargePercent)) {
            zarAmount.value = null;
            surcharge.value = 0;
            return;
        }

        let amountZAR = foreignAmount.value * rate;
        amountZAR += (amountZAR * surchargePercent) / 100;

        zarAmount.value = parseFloat(amountZAR.toFixed(2));
        surcharge.value = surchargePercent.toFixed(2);
    } catch (error) {
        console.error("Error in calculateZAR:", error);
        zarAmount.value = null;
        surcharge.value = 0;
    }
}

function calculateForeign() {
    try {
        const currency = currencies.value.find(
            (c) => c.code === selectedCurrencyCode.value
        );
        if (!currency || !zarAmount.value) {
            foreignAmount.value = null;
            surcharge.value = 0;
            return;
        }

        const rate = parseFloat(currency.exchange_rate);
        const surchargePercent = parseFloat(currency.surcharge_percentage);

        if (isNaN(rate) || isNaN(surchargePercent)) {
            foreignAmount.value = null;
            surcharge.value = 0;
            return;
        }

        let amountForeign = zarAmount.value / rate;
        amountForeign = amountForeign / (1 + surchargePercent / 100);

        foreignAmount.value = parseFloat(amountForeign.toFixed(2));
        surcharge.value = surchargePercent.toFixed(2);
    } catch (error) {
        console.error("Error in calculateForeign:", error);
        foreignAmount.value = null;
        surcharge.value = 0;
    }
}

async function submitOrder() {
    if (!selectedCurrencyCode.value) {
        Swal.fire("Error", "Please select a currency", "error");
        return;
    }
    if (!foreignAmount.value) {
        Swal.fire("Error", "Please enter foreign amount", "error");
        return;
    }

    const currency = currencies.value.find(
        (c) => c.code === selectedCurrencyCode.value
    );
    const surchargeAmount =
        (zarAmount.value * parseFloat(currency.surcharge_percentage)) / 100;

    loading.value = true;

    try {
        const res = await axios.post("/api/orders", {
            currency_id: currency.id,
            currency_code: currency.code,
            foreign_amount: foreignAmount.value,
            zar_amount: zarAmount.value,
            exchange_rate: currency.exchange_rate,
            surcharge_percentage: currency.surcharge_percentage,
            surcharge_amount: surchargeAmount,
            discount_percentage: 0,
        });

        if (res.data.success) {
            Swal.fire("Success", "Order placed successfully", "success");
            foreignAmount.value = null;
            zarAmount.value = null;
            selectedCurrencyCode.value = "";
        } else {
            Swal.fire("Error", res.data.msg || "Unknown error", "error");
        }
    } catch (e) {
        Swal.fire("Error", "Failed to place order", "error");
        console.error("Order submission error:", e.response?.data || e.message);
    } finally {
        loading.value = false;
    }
}

onMounted(async () => {
    loadingCurrencies.value = true;
    try {
        const res = await axios.get("/api/currencies");
        if (res.data.success) {
            currencies.value = res.data.data;
        } else {
            Swal.fire("Error", "Failed to load currencies", "error");
        }
    } catch (e) {
        Swal.fire("Error", "Failed to fetch currencies", "error");
        console.error(e);
    } finally {
        loadingCurrencies.value = false;
    }
});
</script>
