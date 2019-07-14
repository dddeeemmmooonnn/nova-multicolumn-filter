<template>
    <div style="flex: 1; align-items: center; justify-content: center;">
        <select
            class="w-full form-control form-select"
            @change="handleChangeColumn"
            :value="column"
        >
            <option value="" selected>&mdash;</option>
            <option v-for="option in columns" :value="option.value">{{option.label}}</option>
        </select>

        <select
            v-if="columnType !== 'select' && columnType !== 'checkbox'"
            class="w-full form-control form-select"
            @change="handleChangeOperator"
            v-model="operator"
        >
            <option value="" selected>&mdash;</option>
            <option v-for="option in operators" :value="option.value">{{option.label}}</option>
        </select>

        <input
            v-if="columnType !== 'select' && columnType !== 'checkbox'"
            :type="columnType"
            v-model="valueDecoded"
            @keydown.stop="handleChangeData"
            @change="handleChangeData"
            class="w-full form-input form-input-bordered form-control"
        />

        <input
            v-if="columnType === 'checkbox'"
            :type="columnType"
            v-model="value"
            @change="handleChangeCheckbox"
            class=" form-control"
        />

        <select
            v-if="columnType === 'select'"
            class="w-full form-control form-select"
            @change="handleChangeSelect"
            v-model="value"
        >
            <option value="" selected>&mdash;</option>
            <option v-for="option in options" :value="option.value">{{option.label}}</option>
        </select>
    </div>
</template>

<script>
    export default {
        props: [
            'columns', 'column', 'operator', 'value', 'index',
        ],

        data() {
            return {
                oldColumnType: this.columnType,
                valueDecoded: decodeURIComponent(this.value),
            }
        },

        methods: {
            handleChange() {
                this.$emit('change', this.index, {
                    column: this.column,
                    operator: this.getOperator,
                    value: this.value,
                });
            },

            handleChangeColumn(event) {
                this.column = event.target.value;
                if (this.columnType !== this.oldColumnType) {
                    this.value = encodeURIComponent(this.valueDecoded = this.defaultValue);
                    this.operator = this.defaultOperator;
                }
                this.oldColumnType = this.columnType;
                this.handleChange();
            },

            handleChangeOperator(event) {
                this.operator = event.target.value;
                this.handleChange();
            },

            handleChangeData(event) {
                this.debouncer(() => {
                    if (event.which != 9) {
                        this.value = encodeURIComponent(this.valueDecoded);
                        this.handleChange();
                    }
                })
            },

            handleChangeCheckbox(event) {
                this.value = this.value ? 1 : 0;
                this.handleChange();
            },

            handleChangeSelect(event) {
                this.value = encodeURIComponent(event.target.value);
                this.handleChange();
            },

            debouncer: _.debounce(callback => callback(), 500),
        },

        computed: {
            columnType() {
                return this.column ? this.columns[this.column].type : 'text';
            },

            operators() {
                return this.column ? this.columns[this.column].operators : [];
            },

            options() {
                return this.column ? this.columns[this.column].options : [];
            },

            getOperator() {
                return this.columnType === 'select' || this.columnType === 'checkbox' ? '=' : this.operator;
            },

            defaultOperator() {
                return this.column && this.columns[this.column].defaultOperator ? this.columns[this.column].defaultOperator : '';
            },

            defaultValue() {
                return this.column && this.columns[this.column].defaultValue ? this.columns[this.column].defaultValue : '';
            },
        },
    }
</script>

<style scoped>

</style>
