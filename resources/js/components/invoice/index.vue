<template>
    <div class="container">
      <div class="invoices">
        <div class="card__header">
          <div>
            <h2 class="invoice__title">Invoices</h2>
          </div>
          <!-- <div>
            <a class="btn btn-secondary" @click="createNewInvoice">New Invoice</a>
          </div> -->
          <div>
  <router-link to="/invoice/new" class="btn btn-secondary">New Invoice</router-link>
</div>

        </div>
  
        <div class="table card__content">
          <!-- Filter Options -->
          <div class="table--filter">
            <span class="table--filter--collapseBtn">
              <i class="fas fa-ellipsis-h"></i>
            </span>
            <div class="table--filter--listWrapper">
              <ul class="table--filter--list">
                <li>
                  <p
                    class="table--filter--link"
                    :class="{ 'table--filter--link--active': filter === 'all' }"
                    @click="filter = 'all'; applyFilter()"
                  >
                    All
                  </p>
                </li>
                <li>
                  <p
                    class="table--filter--link"
                    :class="{ 'table--filter--link--active': filter === 'paid' }"
                    @click="filter = 'paid'; applyFilter()"
                  >
                    Paid
                  </p>
                </li>
              </ul>
            </div>
          </div>
  
          <!-- Search Section -->
          <div class="table--search">
            <div class="table--search--wrapper">
              <select class="table--search--select" v-model="filter" @change="applyFilter">
                <option value="all">All</option>
                <option value="paid">Paid</option>
              </select>
            </div>
            <div class="relative">
              <i class="table--search--input--icon fas fa-search"></i>
              <input
                class="table--search--input"
                type="text"
                placeholder="Search invoice"
                v-model="searchQuery"
                @input="debounce(applyFilter, 300)"
              />
            </div>
          </div>
  
          <!-- Table Header -->
          <div class="table--heading">
            <p>ID</p>
            <p>Date</p>
            <p>Number</p>
            <p>Customer</p>
            <p>Due Date</p>
            <p>Total</p>
          </div>
  
          <!-- Invoice Items -->
          <div
            class="table--items"
            v-for="invoice in filteredInvoices"
            :key="invoice.id"
          >
            <a href="#" class="table--items--transactionId">{{ invoice.id }}</a>
            <p>{{ invoice.date || 'N/A' }}</p>
            <p>{{ invoice.number }}</p>
            <p>{{ invoice.customer ? invoice.customer.name : 'N/A' }}</p>
            <p>{{ invoice.due_date || 'N/A' }}</p>
            <p>$ {{ invoice.total ? invoice.total : 'N/A' }}</p>
          </div>
  
          <!-- No Results Message -->
          <div v-if="filteredInvoices.length === 0" class="no-results">
            No invoices found matching your search.
          </div>
  
          <!-- Loading State -->
          <div v-if="loading" class="loading-spinner">Loading...</div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import { useRouter } from "vue-router"; // Correcting import
  
  export default {
    data() {
      return {
        userData: [], // All invoices fetched from the server
        filteredData: [], // Filtered invoices for display
        searchQuery: "", // Text entered in the search input
        filter: "all", // Selected filter option
        errorMessage: "",
        loading: true,
      };
    },
    mounted() {
      this.fetchInvoices();
    },
    computed: {
      // Computed property for filtered invoices
      filteredInvoices() {
        return this.userData.filter((invoice) => {
          const matchesSearch =
            invoice.number.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            (invoice.customer?.name || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            (invoice.due_date || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            (invoice.total || '').toString().includes(this.searchQuery.toLowerCase()) ||
            (invoice.id || '').toString().includes(this.searchQuery.toLowerCase());
  
          const matchesFilter =
            this.filter === 'all' ||
            (this.filter === 'paid' && invoice.status === 'paid');
  
          return matchesSearch && matchesFilter;
        });
      },
    },
    methods: {
      // Fetch invoices from the server
      async fetchInvoices() {
        try {
          const response = await axios.get(`/api/invoices`, {
            headers: {
              "Content-Type": "application/json",
            },
          });
  
          if (response.data && response.data.data) {
            this.userData = response.data.data;
          } else {
            this.errorMessage = "No user data found";
          }
        } catch (error) {
          console.error("Error:", error);
          this.errorMessage = "Failed to load user data";
        } finally {
          this.loading = false;
        }
      },
  
      // Apply search and filter logic
      applyFilter() {
        this.filteredData = this.userData.filter((invoice) => {
          const matchesSearch =
            invoice.number.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            (invoice.customer?.name || '').toLowerCase().includes(this.searchQuery.toLowerCase());
  
          const matchesFilter =
            this.filter === 'all' ||
            (this.filter === 'paid' && invoice.status === 'paid');
  
          return matchesSearch && matchesFilter;
        });
      },
  
      // Debounce function to optimize search input
      debounce(func, delay) {
        let timeout;
        return (...args) => {
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(this, args), delay);
        };
      },
  
      // Method to create a new invoice
      async createNewInvoice() {
        try {
          let form = await axios.get('/api/create_invoice'); // Fetch form data if necessary
          console.log('form', form.data);
          const router = useRouter(); // Corrected router usage
          router.push('/invoice/new'); // Navigate to the new invoice page
        } catch (error) {
          console.error('Error creating new invoice:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .no-results {
    text-align: center;
    margin-top: 20px;
    font-size: 16px;
    color: gray;
  }
  
  .loading-spinner {
    text-align: center;
    font-size: 18px;
    color: gray;
    margin-top: 20px;
  }
  </style>
  