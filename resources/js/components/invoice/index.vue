<template>
    <div class="container">
      <div class="invoices">
        <div class="card__header">
          <div>
            <h2 class="invoice__title">Invoices</h2>
          </div>
          <div>
            <a class="btn btn-secondary">New Invoice</a>
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
                @input="applyFilter"
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
            v-for="invoice in filteredData"
            :key="invoice.id"
          >
            <a href="#" class="table--items--transactionId">{{ invoice.id }}</a>
            <p>{{ invoice.date }}</p>
            <p>{{ invoice.number }}</p>
            <p>{{ invoice.customer ? invoice.customer.name : 'N/A' }}</p> <!-- Display customer name -->
            <p>{{ invoice.due_date }}</p>
            <p>$ {{ invoice.total }}</p>
          </div>
  
          <!-- No Results Message -->
          <div v-if="filteredData.length === 0" class="no-results">
            No invoices found matching your search.
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
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
            this.filteredData = response.data.data; // Initialize with all data
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
            invoice.number
              .toString()
              .toLowerCase()
              .includes(this.searchQuery.toLowerCase()) ||
            invoice.customer_id
              .toString()
              .toLowerCase()
              .includes(this.searchQuery.toLowerCase());
  
          const matchesFilter =
            this.filter === "all" ||
            (this.filter === "paid" && invoice.status === "paid");
  
          return matchesSearch && matchesFilter;
        });
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
  </style>
  