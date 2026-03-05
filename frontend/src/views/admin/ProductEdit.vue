<template>
  <div class="product-edit-admin">
    <h2>Edit Product</h2>
    <form @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" v-model="product.name" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" v-model="product.description" rows="3"></textarea>
      </div>
      <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select id="category" v-model="product.category_id" class="form-select" required>
          <option value="" disabled>Select category</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <div class="mt-2">
          <router-link to="/admin/categories" class="text-decoration-none">
            Manage categories
          </router-link>
        </div>
      </div>
      <div class="mb-3">
        <label for="image" class="form-label">Product Image</label>
        <input type="file" class="form-control" id="image" @change="handleImageChange" accept="image/*">
        <div v-if="product.image" class="mt-2">
          <p>Current Image:</p>
          <img :src="`http://localhost:8000/storage/${product.image}`" alt="Current Product Image" style="width: 100px; height: 100px; object-fit: cover;">
        </div>
      </div>

      <!-- Variants Section -->
      <div class="mb-4">
        <h4>Product Variants</h4>
        <div class="card p-3 mb-3">
          <div class="row g-2 align-items-end">
            <div class="col-md-2">
              <label class="form-label small">Color</label>
              <select v-model="newVariant.color_id" class="form-select form-select-sm">
                <option value="" disabled>Select Color</option>
                <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Size</label>
              <select v-model="newVariant.size_id" class="form-select form-select-sm">
                <option value="" disabled>Select Size</option>
                <option v-for="size in sizes" :key="size.id" :value="size.id">{{ size.name }}</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Stock</label>
              <input type="number" v-model="newVariant.stock" class="form-control form-control-sm" min="0">
            </div>
            <div class="col-md-2">
              <label class="form-label small">Price</label>
              <input type="number" v-model="newVariant.price" class="form-control form-control-sm" min="0.01" step="0.01" placeholder="Required">
            </div>
            <div class="col-md-2">
              <label class="form-label small">Variant Images</label>
              <input type="file" class="form-control form-control-sm" accept="image/*" multiple @change="handleVariantImageChange">
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-sm btn-success w-100" @click="addVariant">Add</button>
            </div>
          </div>
        </div>

        <!-- Variants List -->
        <table class="table table-bordered table-sm" v-if="variants.length > 0">
          <thead>
            <tr>
              <th>Color</th>
              <th>Size</th>
              <th>Stock</th>
              <th>Price</th>
              <th>Images</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(variant, index) in variants" :key="index">
              <td>{{ getColorName(variant.color_id) }}</td>
              <td>{{ getSizeName(variant.size_id) }}</td>
              <td>{{ variant.stock }}</td>
              <td>${{ Number(variant.price).toFixed(2) }}</td>
              <td>
                <div class="small">{{ (variant.existing_image_paths?.length || 0) + (variant.imageFiles?.length || 0) }} file(s)</div>
                <div class="d-flex gap-1 flex-wrap mt-1" v-if="variant.existing_image_paths?.length">
                  <img
                    v-for="(path, imgIndex) in variant.existing_image_paths"
                    :key="`${index}-${imgIndex}`"
                    :src="`http://localhost:8000/storage/${path}`"
                    alt="Variant"
                    style="width: 28px; height: 28px; object-fit: cover; border-radius: 4px;"
                  />
                </div>
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-danger" @click="removeVariant(index)">Remove</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <button type="submit" class="btn btn-primary">Update Product</button>
      <router-link to="/admin/products" class="btn btn-secondary ms-2">Cancel</router-link>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useCategories } from '@/composables/useCategories.js'

const route = useRoute()
const router = useRouter()
const { categories, fetchCategories } = useCategories()

const colors = ref([])
const sizes = ref([])
const variants = ref([])
const newVariantImages = ref([])
const newVariant = ref({
  color_id: '',
  size_id: '',
  stock: 0,
  price: '',
})

const product = ref({
  id: null,
  name: '',
  description: '',
  category_id: '',
  image: '' // This will store the image path from the backend
})

const imageFile = ref(null) // To store the actual file object for upload

const handleImageChange = (event) => {
  imageFile.value = event.target.files[0]
}

const handleVariantImageChange = (event) => {
  newVariantImages.value = Array.from(event.target.files || [])
}

const fetchColorsAndSizes = async () => {
  try {
    const [colorsRes, sizesRes] = await Promise.all([
      axios.get('/colors'),
      axios.get('/sizes')
    ])
    colors.value = colorsRes.data
    sizes.value = sizesRes.data
  } catch (error) {
    console.error('Error fetching attributes:', error)
  }
}

const addVariant = () => {
  if (!newVariant.value.color_id && !newVariant.value.size_id) {
    alert('Please select at least a color or a size')
    return
  }
  
  // Check if variant already exists
  const exists = variants.value.some(v => 
    v.color_id === newVariant.value.color_id && 
    v.size_id === newVariant.value.size_id
  )
  
  if (exists) {
    alert('This variant already exists')
    return
  }

  if (!newVariant.value.price || Number(newVariant.value.price) <= 0) {
    alert('Please add a valid variant price')
    return
  }

  variants.value.push({
    ...newVariant.value,
    imageFiles: [...newVariantImages.value],
    existing_image_paths: [],
  })
  
  // Reset new variant form
  newVariant.value = {
    color_id: '',
    size_id: '',
    stock: 0,
    price: ''
  }
  newVariantImages.value = []
}

const removeVariant = (index) => {
  variants.value.splice(index, 1)
}

const getColorName = (id) => colors.value.find(c => c.id === id)?.name || '-'
const getSizeName = (id) => sizes.value.find(s => s.id === id)?.name || '-'

onMounted(async () => {
  product.value.id = route.params.id
  try {
    await fetchCategories()
    await fetchColorsAndSizes()
    const response = await axios.get(`/products/${product.value.id}`)
    product.value = {
      ...response.data,
      category_id: response.data.category_id || response.data.category?.id || ''
    }
    // Map variants from backend
    if (response.data.variants) {
      variants.value = response.data.variants.map(v => ({
        color_id: v.color_id,
        size_id: v.size_id,
        stock: v.stock,
        price: v.price,
        imageFiles: [],
        existing_image_paths: (v.images || []).map(img => img.path),
      }))
    }
  } catch (error) {
    console.error('Error fetching product:', error)
    alert('Failed to fetch product for editing.')
    router.push('/admin/products')
  }
})

const handleSubmit = async () => {
  try {
    const formData = new FormData()
    formData.append('_method', 'PUT') // Laravel expects _method for PUT requests with FormData
    formData.append('name', product.value.name)
    formData.append('description', product.value.description)
    formData.append('category_id', product.value.category_id)
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }
    
    const variantsPayload = variants.value.map((variant) => ({
      color_id: variant.color_id || null,
      size_id: variant.size_id || null,
      stock: variant.stock || 0,
      price: variant.price,
      existing_image_paths: variant.existing_image_paths || [],
    }))
    formData.append('variants', JSON.stringify(variantsPayload))

    variants.value.forEach((variant, index) => {
      ;(variant.imageFiles || []).forEach((file) => {
        formData.append(`variant_images[${index}][]`, file)
      })
    })

    await axios.post(`/products/${product.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    alert('Product updated successfully!')
    router.push('/admin/products')
  } catch (error) {
    console.error('Error updating product:', error)
    alert('Failed to update product.')
  }
}
</script>

<style scoped>
.product-edit-admin {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
