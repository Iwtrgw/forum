<template>
	<div>
		<div v-for="(reply,index) in items" :key="reply.id">
			<reply :reply="reply" @deleted="remove(index)"></reply>
		</div>

		<paginator :dataSet="dataSet" @changed="fetch"></paginator>

		<new-reply :endpoint="endpoint" @created="add"></new-reply>
	</div>
</template>

<script type="text/javascript">
	import Reply from './Reply';
	import NewReply from './NewReply';
	import collection from '../mixins/Collection';

	export default {
		components: { Reply,NewReply },

		mixins: [collection],

		data() {
			return { 
				dataSet:false,
				endpoint: location.pathname+'/replies'
			}
		},

		created() {
			this.fetch();
		},

		methods: {
			fetch(page) {
				axios.get(this.url(page)).then(this.refresh);
			},

			url(page) {
				if (!page) {
					let query = location.search.match(/page=(\d+)/);

					page = query ? query[1] : 1;
				}

				return `${location.pathname}/replies?page=${page}`;
			},

			refresh({data}) {
				this.dataSet = data;
				this.items = data.data;

				window.scrollTo(0,0);
			}
		}
	}
</script>