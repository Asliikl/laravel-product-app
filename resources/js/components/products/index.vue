<script setup>
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router";

    const router =useRouter()

    let products = ref([])

    onMounted(async () => {
        getProducts()
    })
    const newProduct = () => {
        router.push('/products/create')
    }

    const ourImage = (img) => {
        return "/upload/"+img
    }

    const getProducts = async() => {
        let response = await axios.get ('/api/products')
        .then((response) =>  {
            products.value = response.data.products
        })
    }

    const editProduct = (id) => {
        router.push(`/products/${id}/edit`);
    }

    const deleteProduct = async (id) => {
        if (confirm("Bu ürünü silmek istediğinize emin misiniz?")) {
            try {
                const response = await axios.delete(`/api/products/${id}`);
                if (response.data.success) {
                    alert('Ürün başarıyla silindi');
                    getProducts();
                }
            } catch (error) {
                console.error('Silme hatası:', error);
                alert('Ürün silinirken bir hata oluştu');
            }
        }
    }
</script>

<template>

    <div class="products__list table  my-3">
              
        <div class="customers__titlebar dflex justify-content-between align-items-center">
            <div class="customers__titlebar--item">
                <h1 class="my-1">Products</h1>
            </div>
            <div class="customers__titlebar--item">
                <button @click ="newProduct" class="btn btn-secondary my-1" >
                    Add Product
                </button>
            </div>
        </div>

        <div class="table--heading mt-2 products__list__heading " style="padding-top: 20px;background:#FFF">
            <p class="table--heading--col1">image</p>
            <p class="table--heading--col2">
                Product
            </p>
            <p class="table--heading--col4">Type</p>
            <p class="table--heading--col3">
                Inventory
            </p>
            <p class="table--heading--col5">actions</p>
        </div>

        <div class="table--items products__list__item" v-for="product in products" :key="product.id" >
            <div class="products__list__item--imgWrapper">
                <img class="products__list__item--img" :src="ourImage(product.image)" style="height: 40px;">
            </div>
            <a href="# " class="table--items--col2">
               {{ product.name }}
            </a>
            <p class="table--items--col2">
                {{ product.type }}
            </p>
            <p class="table--items--col3">
                {{ product.quantity }}
            </p>     
            <div>     
                <button class="btn-icon btn-icon-success" @click="editProduct(product.id)">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button class="btn-icon btn-icon-danger" @click="deleteProduct(product.id)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>

    </div>



</template>