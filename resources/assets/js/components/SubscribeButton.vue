<template>
	<button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script type="text/javascript">
	export default {
        props: ['active'],

        computed: {
          classes() {
              return ['btn',this.active ? 'btn-primary' : 'btn-default'];
          }
        },

        methods:{
            subscribe(){
                axios[
                        (this.active ? 'delete' : 'post')
                    ](location.pathname + '/subscriptions');

                this.active = ! this.active;
            }
        }
    }
</script>