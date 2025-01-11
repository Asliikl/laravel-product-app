<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();

const form = reactive({
    name: "",
    description: "",
    image: "",
    type: "",
    quantity: "",
    price: ""
});

let errors = ref([]);
const editMode = ref(false);

onMounted(() => {
    if (route.params.id) {
        editMode.value = true;
        getProduct();
    }
});

const getProduct = async () => {
    try {
        const response = await axios.get(`/api/products/${route.params.id}/edit`);
        const product = response.data.product;
        
        // Form verilerini doldur
        form.name = product.name;
        form.description = product.description;
        form.image = product.image;
        form.type = product.type;
        form.quantity = product.quantity;
        form.price = product.price;
    } catch (error) {
        console.error('Ürün yüklenirken hata:', error);
        alert('Ürün bilgileri yüklenemedi');
    }
};

const getImage = () => {
    let image = "/upload/no-image.jpg"; 
    if (form.image) {
        if (form.image.indexOf("base64") !== -1) {
            image = form.image; 
        } else {
            image = "/upload/" + form.image; 
        }
    }
    return image;
};

const handleFileChange = (e) => {
    let file = e.target.files[0];
    let reader = new FileReader();
    reader.onloadend = () => {
        form.image = reader.result; 
    };
    reader.readAsDataURL(file); 
};

const handleSave = async () => {
    try {
        let url = '/api/products';
        let method = 'post';

        // Eğer edit modundaysa
        if (route.params.id) {
            url = `/api/products/${route.params.id}`;
            method = 'put';
        }

        const response = await axios[method](url, form);

        if (response.data.success) {
            alert(route.params.id ? 'Ürün güncellendi' : 'Ürün eklendi');
            router.push('/products');
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            alert('Bir hata oluştu: ' + error.message);
        }
    }
};
</script>

<template>
   <div class="products__create ">

    <div class="products__create__titlebar dflex justify-content-between align-items-center">
        <div class="products__create__titlebar--item">
            <h1 class="my-1" v-if="editMode">Edit </h1>
            <h1 class="my-1" v-else>Create Product</h1>
        </div>
        <div class="products__create__titlebar--item">
            <button @click="handleSave" class="btn btn-secondary ml-1">Save</button>
        </div>
    </div>

    <div class="products__create__cardWrapper mt-2">
        <div class="products__create__main">
            <div class="products__create__main--addInfo card py-2 px-2 bg-white">
                <p class="mb-1">Name</p>
                <input type="text" class="input" v-model="form.name" />
                <small class="text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
                
                <p class="my-1">Description (optional)</p>
                <textarea cols="10" rows="5" class="textarea" v-model="form.description"></textarea>
                <small style="color:red;" v-if="errors.description">{{errors.description }}</small>

                <div class="products__create__main--media--images mt-2">
                   <ul class="products__create__main--media--images--list list-unstyled">
                       <li class="products__create__main--media--images--item">
                           <div class="products__create__main--media--images--item--imgWrapper">
                               <img :src="getImage()" class="products__create__main--media--images--item--img" alt="Product Image" />
                           </div>
                       </li>
                       <!-- Upload image -->
                       <li class="products__create__main--media--images--item">
                           <form class="products__create__main--media--images--item--form">
                               <input class="products__create__main--media--images--item--form--input" 
                                      type="file" 
                                      id="myfile" 
                                      @change="handleFileChange" />
                               <label class="products__create__main--media--images--item--form--label" for="myfile">
                                   Resim Ekle
                               </label>
                           </form>
                           <small class="upload-text">Resim yüklemek için tıklayın</small>
                       </li>
                   </ul>
               </div>
            </div>
        </div>

        <div class="products__create__sidebar">
            <div class="card py-2 px-2 bg-white">
                <!-- Product type -->
                <div class="my-3">
                    <p>Category</p>
                    <input type="text" class="input" v-model="form.type" />
                    <small class="text-danger" v-if="errors.type">{{ errors.type[0] }}</small>
                </div>
                <hr>

                <div class="my-3">
                    <p>Inventory</p>
                    <input type="text" class="input" v-model="form.quantity" />
                    <small class="text-danger" v-if="errors.quantity">{{ errors.quantity[0] }}</small>
                </div>
                <hr>

                <div class="my-3">
                    <p>Price</p>
                    <input type="text" class="input" v-model="form.price" step="0.01" />
                    <small class="text-danger" v-if="errors.price">{{ errors.price[0] }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bar -->
    <div class="dflex justify-content-between align-items-center my-3">
        <p></p>
        <button @click="handleSave" class="btn btn-secondary">Save</button>
    </div>

</div>
</template>
