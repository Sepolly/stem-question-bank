<script setup lang="ts">
import DashboardController from '@/actions/App/Http/Controllers/DashboardController';
import { useAuth } from '@/composables/useAuth';
import useEvent from '@/composables/useEvent';
import { Question, Subject } from '@/types';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const {SignIn,SignUp,currentUser,userHasEvent} = useAuth()
const {currentEvent} = useEvent()

defineProps<{
  subjects: Subject[]
}>()
  
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">

    <!-- NAVIGATION -->
    <nav class="w-full py-5 px-6 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-tight">STEMBank</div>

      <div class="hidden md:flex space-x-10 text-sm font-medium">
        <a href="#" class="hover:text-blue-600">Features</a>
        <a href="#" class="hover:text-blue-600">Subjects</a>
        <a href="#" class="hover:text-blue-600">Pricing</a>
        <a href="#" class="hover:text-blue-600">Docs</a>
      </div>

      <div v-if="currentUser && userHasEvent">
        <button @click="router.get(DashboardController({event: currentEvent.id}))" class="cursor-pointer px-5 py-2 bg-blue-600 text-white font-semibold rounded-xl shadow hover:bg-blue-700">
            Dashboard
        </button>
      </div>
      <div v-else class="flex items-center space-x-4">
        <button @click="SignIn" class="cursor-pointer px-4 py-2 rounded-lg font-medium hover:bg-gray-200">
          Sign In
        </button>
        <button @click="SignUp" class="cursor-pointer px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow">
          Get Started
        </button>
      </div>
    </nav>

    <!-- HERO -->
    <section class="w-full px-6 pt-10 pb-24 flex flex-col items-center">

      <!-- Floating UI Elements -->
      <div class="relative w-full max-w-6xl">

        <!-- Left Sticky Note -->
        <div class="absolute -left-10 top-16 hidden lg:block">
          <div class="bg-yellow-100 p-4 w-40 rounded-lg shadow-lg rotate-[-6deg]">
            <p class="text-sm font-medium">
              Create subjects,  
              <br>add topics & questions
            </p>
          </div>
        </div>
       
        <!-- Right Side Cards -->
        <div class="flex relative">
          <div v-for="(subject,idx) in subjects" :key="idx" :class="`absolute -right-10 top-${20+idx} -rotate${'-' + 4+idx} hidden lg:block space-y-4`">
            <div v-if="subject.questionCount > 5" class="bg-white p-4 rounded-xl shadow-lg w-48">
              <p class="text-sm font-semibold">{{ subject.name }} Subject</p>
              <p class="text-xs text-gray-600">{{ subject.topics.length }} topics · {{ subject.questionCount }} questions</p>
            </div>
          </div>
        </div>

        <!-- HERO CENTER -->
        <div class="text-center max-w-3xl mx-auto">
          <div class="flex justify-center mb-6">
            <div class="w-14 h-14 bg-gray-200 rounded-2xl flex items-center justify-center shadow-inner">
              <div class="grid grid-cols-2 gap-1">
                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
              </div>
            </div>
          </div>

          <h1 class="text-4xl md:text-6xl font-bold tracking-tight">
            Build and manage STEM
            <span class="text-gray-400">question banks</span>
          </h1>

          <p class="mt-6 text-lg text-gray-600">
            A modern platform for teachers and institutions to create, manage, and 
            share exam-ready STEM questions efficiently.
          </p>

          <div class="mt-8 flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-6">
            <button @click="()=>router.get(DashboardController.noEvent())" class="cursor-pointer px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow hover:bg-blue-700">
              Create a Question Bank
            </button>
            <button class="cursor-pointer px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-100">
              Watch Demo
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="px-6 py-20 bg-white">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-14">Why use STEMBank?</h2>

        <div class="grid md:grid-cols-3 gap-10">

          <div class="p-6 bg-gray-50 rounded-2xl shadow-sm">
            <h3 class="font-semibold text-lg mb-2">Subjects & Topics</h3>
            <p class="text-gray-600 text-sm">
              Create structured subjects, add topics, and track question counts effortlessly.
            </p>
          </div>

          <div class="p-6 bg-gray-50 rounded-2xl shadow-sm">
            <h3 class="font-semibold text-lg mb-2">Powerful Question Builder</h3>
            <p class="text-gray-600 text-sm">
              Craft MCQs, structured questions, and diagram-based problems with ease.
            </p>
          </div>

          <div class="p-6 bg-gray-50 rounded-2xl shadow-sm">
            <h3 class="font-semibold text-lg mb-2">Team Collaboration</h3>
            <p class="text-gray-600 text-sm">
              Add teachers, examiners, and reviewers with adjustable permission levels.
            </p>
          </div>

        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-10 text-center text-sm text-gray-500">
      © {{ new Date().getFullYear() }} STEMBank — All rights reserved.
    </footer>

  </div>
</template>
