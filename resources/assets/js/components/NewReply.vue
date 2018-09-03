<template>
	<div>
		<div v-if="signedIn">
			<div class="form-group">
				<textarea class="form-control" name="body" id="body" rows="5" required placeholder="说点什么吧..." v-model="body"></textarea>
			</div>

			<button class="btn btn-default" type="submit" @click="addReply">提交</button>
		</div>

		<p class="text-center" v-else>
			请先<a href="/login">登录</a>，然后再发表回复
		</p>
	</div>
</template>

<script type="text/javascript">
	export default {
		props: ['endpoint'],

		data() {
			return {
				body: '',
			};
		},

		computed: {
			signedIn(){
				return window.App.signIn;
			}
		},

		methods: {
			addReply() {
				axios.post(this.endpoint,{ body:this.body })
					 .then(({data}) => {
					 	this.body = '';
					 	flash('Your reply has been posted.');

					 	this.$emit('created',data);
					 });
			}
		}
	}
</script>