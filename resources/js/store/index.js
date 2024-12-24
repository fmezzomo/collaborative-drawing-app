import { createStore } from 'vuex';

const store = createStore({
  state() {
    return {
      canvasHistory: [],
      undoHistory: [],
    };
  },
  mutations: {
    addToHistory(state, data) {
      state.canvasHistory.push(data);
    },

    addToUndoHistory(state, data) {
      state.undoHistory.push(data);
    },

    undo(state) {
      if (state.canvasHistory.length > 0) {
        const history = state.canvasHistory.pop();
        state.undoHistory.push(history);
      }
    },

    redo(state) {
      if (state.undoHistory.length > 0) {
        const undo = state.undoHistory.pop();
        state.canvasHistory.push(undo);
      }
    },
  },
  actions: {
    updateCanvas({ commit }, data) {
      commit('addToHistory', data);
    },
  },
});

export default store;
