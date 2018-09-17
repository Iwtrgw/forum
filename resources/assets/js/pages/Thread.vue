<script type="text/javascript">
	import Replies from '../components/Replies';
	import SubscribeButton from '../components/SubscribeButton';

	export default {
		props: ['initialRepliesCount',
		'dataLocked'],

		components: { Replies,SubscribeButton },

		data() {
			return {
				repliesCount:this.dataRepliesCount,
				locked:this.dataLocked,
			}
		}
	}
</script>