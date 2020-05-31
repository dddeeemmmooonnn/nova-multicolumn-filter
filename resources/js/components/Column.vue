<template>
    <div class="nova-multicolumn-filter__column">
        <select
            class="w-full form-control form-select nova-multicolumn-filter__column-select"
            @change="handleChangeColumn"
            :value="column_"
        >
            <option value="" selected>{{__('multicolumn.select_empty_label')}}</option>
            <option v-for="option in columns" :value="option.value">{{option.label}}</option>
        </select>

        <select
            v-if="columnType !== 'select' && columnType !== 'checkbox'"
            class="w-full form-control form-select nova-multicolumn-filter__operator-select"
            @change="handleChangeOperator"
            :value="operator_"
        >
            <option value="">{{__('multicolumn.select_empty_label')}}</option>
            <option v-for="option in operators" :value="option.value">{{option.label}}</option>
        </select>

        <input
            v-if="columnType !== 'select' && columnType !== 'checkbox'"
            :type="columnType"
            v-model="valueDecoded"
            :placeholder="placeholder"
            @keydown.stop="handleChangeData"
            @change="handleChangeData"
            class="w-full form-input form-input-bordered form-control nova-multicolumn-filter__text"
        />

        <input
            v-if="columnType === 'checkbox'"
            :type="columnType"
            v-model="value_"
            @change="handleChangeCheckbox"
            class="form-control nova-multicolumn-filter__checkbox"
        />

        <select
            v-if="columnType === 'select'"
            class="w-full form-control form-select nova-multicolumn-filter__select"
            @change="handleChangeSelect"
            :value="value_"
        >
            <option value="" selected>{{__('multicolumn.select_empty_label')}}</option>
            <option v-for="option in options" :value="option.value">{{option.label}}</option>
        </select>
    </div>
</template>

<script>
    export default {
        props: [
            'columns', 'column', 'operator', 'value', 'index', 'uid',
        ],

        data() {
            return {
                oldColumnType: this.columnType,
                valueDecoded: decodeURIComponent(this.value),
                value_: this.value,
                column_: this.column,
                operator_: this.operator,
            }
        },

        methods: {
            handleChange() {
                this.$emit('change', this.index, {
                    column: this.column_,
                    operator: this.getOperator,
                    value: this.value_,
                    column_uid: this.uid,
                });
            },

            handleChangeColumn(event) {
                this.column_ = event.target.value;
                if (this.columnType !== this.oldColumnType) {
                    this.value_ = encodeURIComponent(this.valueDecoded = this.defaultValue);
                    this.operator_ = this.defaultOperator;
                }
                this.oldColumnType = this.columnType;
                this.handleChange();
            },

            handleChangeOperator(event) {
                this.operator_ = event.target.value;
                this.handleChange();
            },

            handleChangeData(event) {
                this.debouncer(() => {
                    if (event.which != 9) {
                        this.value_ = encodeURIComponent(this.valueDecoded.trim());
                        this.handleChange();
                    }
                })
            },

            handleChangeCheckbox(event) {
                this.value_ = this.value_ ? 1 : 0;
                this.handleChange();
            },

            handleChangeSelect(event) {
                this.value_ = event.target.value;
                this.handleChange();
            },

            debouncer: _.debounce(callback => callback(), 500),
        },

        computed: {
            columnType() {
                return this.column_ ? this.columns[this.column_].type : 'text';
            },

            operators() {
                return this.column_ ? this.columns[this.column_].operators : [];
            },

            options() {
                return this.column_ ? this.columns[this.column_].options : [];
            },

            placeholder() {
                return this.column_ ? this.columns[this.column_].placeholder : '';
            },

            getOperator() {
                return this.columnType === 'select' || this.columnType === 'checkbox' ? '=' : this.operator_;
            },

            defaultOperator() {
                return this.column_ && this.columns[this.column_].defaultOperator ? this.columns[this.column_].defaultOperator : '';
            },

            defaultValue() {
                return this.column_ && this.columns[this.column_].defaultValue ? this.columns[this.column_].defaultValue : '';
            },
        },
    }
</script>

<style scoped>
    .nova-multicolumn-filter__column {
        flex: 1;
        align-items: center;
        justify-content: center;
    }
</style>
