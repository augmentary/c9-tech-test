<script setup>
import { reactive, ref } from 'vue'

const loaded = ref(false)
const countries = ref(null)
const shippingMethods = ref(null)

const promises = [
  fetch('/api/v1/country')
    .then((v) => v.json())
    .then((v) => (countries.value = v.data)),
  fetch('/api/v1/shipping-method')
    .then((v) => v.json())
    .then((v) => (shippingMethods.value = v.data))
]

Promise.all(promises).then(() => (loaded.value = true))

const form = reactive({})
const formErrors = ref({})
const result = ref(null)

async function calculate() {
  const err = {}

  if (!form.orderDate) {
    err.orderDate = 'Required value'
  }
  if (!form.country) {
    err.country = 'Required value'
  }
  if (!form.shippingMethod) {
    err.shippingMethod = 'Required value'
  }

  formErrors.value = err

  if (Object.keys(err).length === 0) {
    //datetime-local doesn't output iso strings, and we don't want milliseconds for the api
    const orderDate = new Date(form.orderDate).toISOString().replace(/\.\d{3}/, '')
    const params = {
      ...form,
      orderDate
    }

    const res = await fetch('/api/v1/delivery-date?' + new URLSearchParams(params).toString())
    result.value = await res.json()
  }
}
</script>

<template>
  <form v-if="loaded" class="p-4">
    <div class="mb-2">
      <label class="block" for="orderDate">Order Date</label>
      <input
        type="datetime-local"
        name="orderDate"
        id="orderDate"
        class="p-1 shadow border rounded"
        v-model="form.orderDate"
      />
      <div class="text-red-500" v-if="formErrors.orderDate">{{ formErrors.orderDate }}</div>
    </div>
    <div class="mb-2">
      <label class="block" for="country">Country</label>
      <select name="country" id="country" class="p-1 shadow border rounded" v-model="form.country">
        <option v-for="c in countries" :key="c.isoCode" :value="c.isoCode">
          {{ c.name }}
        </option>
      </select>
      <div class="text-red-500" v-if="formErrors.country">{{ formErrors.country }}</div>
    </div>
    <div class="mb-2">
      <label class="block" for="shippingMethod">Shipping Method</label>
      <select
        name="shippingMethod"
        id="shippingMethod"
        class="p-1 shadow border rounded"
        v-model="form.shippingMethod"
      >
        <option v-for="m in shippingMethods" :key="m.id" :value="m.id">
          {{ m.name }}
        </option>
      </select>
      <div class="text-red-500" v-if="formErrors.shippingMethod">
        {{ formErrors.shippingMethod }}
      </div>
    </div>

    <button type="button" class="mt-2 bg-blue-500 p-2 text-white rounded" @click="calculate">
      Calculate
    </button>
  </form>

  <div v-if="result" class="p-4">
    <h2>Result:</h2>
    {{ result.data.expected_delivery_date }}
  </div>
</template>
