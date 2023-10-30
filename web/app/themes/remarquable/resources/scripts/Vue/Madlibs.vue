<template>
  <div class="madlib bg-grey-400 shadow-grey-light relative z-30 px-4 md:px-8 py-8"
    v-if="!loading">
    <div class="flex flex-col gap-y-6 w-full items-center">
      <div class="flex flex-col md:flex-row md:flex-wrap gap-y-4">
        <div class="entries chapeau md:inline-block" v-for="(question, index) in questions"
          :key="question.text + index">
          <template v-if="question.isVisible">
            <span class="text-grey-100 font-text-chapeau font-bold flex flex-col md:flex md:flex-row gap-y-1 md:items-center">
              <span class="w-max whitespace-nowrap mr-4">
                {{ question.text }}
              </span>
              <span class="relative" @change="rebuild">
                <span @click="openSubMenu(question)" class="selected pb-4 font-bold cursor-pointer mr-4 leading-10">
                  {{ question.currentValue.text }}
                  <span class="ml-2 caret"></span>
                </span>
                <transition name="fade">
                  <span class="submenu shadow z-30 rounded-md" v-show="question.isMenuVisible">
                    <span class="block text-sm cursor-pointer py-3" @click="handleChange(question, value)"
                      v-for="value in question.values">
                      {{ value.text }}
                    </span>
                  </span>
                </transition>
              </span>
            </span>
          </template>
        </div>
      </div>
      <button @click="redirect" class="btn relative ml-3 p-4 text-white flex group items-center justify-center gap-x-3 bg-red-0 hover:bg-red-200 border border-red-0 hover:border-red-200"
        :title="title_Btn" :aria-label="title_Btn">
        <span class="hidden md:block">{{ title_Btn }}</span>
        <span class="md:hidden">{{ title_Btn_Mobile }}</span>
        <div class="absolute w-full h-full top-3 -left-3 bg-transparent border transition border-red-0 group-hover:border-red-200 z-0"></div>
      </button>
    </div>
    <p class="text-grey-500 text-xs pt-2 pb-4 flex md:justify-end">{{ legende }}</p>
  </div>
</template>

<script>

export default {
  name: 'Madlibs',
  data: () => ({
    local: [],
    questions: null,
    selected: {},
    redirect_link: null,
    loading: true,
    answers: [],
    title_Btn: 'Titre du bouton',
    title_Btn_Mobile: 'Titre du bouton en mobile',
    legende: '',
  }),
  beforeMount() {
    this.madlibs = madlibs;
    this.contracts = contracts;
    this.title_Btn = titleBtn;
    this.title_Btn_Mobile = titleBtnMobile;
    this.legende = legende;

    this.madlibs.map((item, index) => {
      item.isVisible = false;
      item.isMenuVisible = false;
      item.currentValue = item.values[0];
      return item;
    });

    this.madlibs[0].isVisible = true;

    this.questions = this.madlibs;

    this.loading = false;

    this.$forceUpdate();

  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      this.rebuild();
    },
    openSubMenu(question) {
      this.questions.forEach(item => {
        if (question === item) {
          return;
        }

        item.isMenuVisible = false;
        return item;
      })

      question.isMenuVisible = !question.isMenuVisible;
    },
    handleChange(question, value) {
      this.questions.map(item => {
        item.isMenuVisible = false;
        return item;
      });
      question.currentValue = value;
      this.$forceUpdate();
      this.rebuild();
    },
    rebuild() {
      this.questions.map(item => {
        item.isVisible = false;
        item.isMenuVisible = false;
        return item;
      });

      this.questions[0].isVisible = true;

      this.questions.filter(item => item.isVisible).forEach(question => {
        let currentValue = question.currentValue;
        let redo = true;

        while (redo) {
          if (currentValue.action_type === "goto") {
            let next = this.questions.find(item => item.id === currentValue.relation_id);

            if (next) {
              currentValue = next.currentValue;
              next.isVisible = true;
            } else {
              redo = false;
            }
          } else {
            this.redirect_link = currentValue.redirect_value.url;
            redo = false;
          }
        }
      });

      this.questions.filter(item => item.isVisible).forEach((question => {
        if (question && question.cookey_key && question.cookey_key !== '') {
          let currentValue = question.currentValue;
          this.cookies.tunnelData.prospect[question.cookey_key] = currentValue.cookie_val;

          const contractData = this.contracts.find(contract => contract.value_needed === currentValue.cookie_val);

          // if (contractData) {
          //   this.cookies.tunnelData.contract = contractData.contract;
          // }
        }
      }))
      this.questions.sort((a, b) => a.order - b.order);
    },
    findQuestionByRelationId(relationId) {
      return this.madlibs.find(question => question.id === relationId)
    },
    getQuestionId(index) {
      return 'question_' + index;
    },
    redirect(e) {
      // this.$cookies.set('ite-tunnel-data-token', this.encodedCookies())
      window.location.href = this.redirect_link;
    }
  }
}
</script>
