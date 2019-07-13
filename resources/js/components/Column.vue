<template>
    <div>
        <select-control
            class="w-full form-control form-select"
            @change="handleChangeColumn"
            :options="columns"
            :value="column"
        >
            <option value="" selected>&mdash;</option>
        </select-control>

        <select-control
            class="w-full form-control form-select"
            @change="handleChangeOperator"
            :options="operators"
            :value="operator"
        >
            <option value="" selected>&mdash;</option>
        </select-control>

        <input
            :type="columnType"
            v-model="search"
            @change="handleChange"
            class="w-full form-input form-input-bordered form-control "
        />
    </div>
</template>

<script>
    export default {
        props: [
            'columns', 'operators', 'column', 'operator', 'search', 'index',
        ],

        data() {
            return {
                oldColumnType: this.columnType,
            }
        },

        mounted() {
            this.search = decodeURIComponent(this.search);
        },

        methods: {
            handleChange() {
                this.$emit('change', this.index, {
                    column: this.column,
                    operator: this.operator,
                    search: encodeURIComponent(this.search),
                });
            },

            handleChangeColumn(event) {
                this.column = event.target.value;
                if (this.columnType !== this.oldColumnType)
                    this.search = '';
                this.oldColumnType = this.columnType;
                this.handleChange();
            },

            handleChangeOperator(event) {
                this.operator = event.target.value;
                this.handleChange();
            },
        },

        computed: {
            columnType() {
                let column = _.find(this.columns, ['value', this.column]);
                return column ? column.type : 'text';
            },
        },
    }
</script>

<style scoped>

</style>
