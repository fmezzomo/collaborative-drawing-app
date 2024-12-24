import { createStore } from 'vuex';

const store = createStore({
  state() {
    return {
      canvasHistory: [],
      undoHistory: [],
      drawingData: null,
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

    setDrawingData(state, data) {
      state.drawingData = data;
    },
  },
  actions: {
    updateCanvas({ commit }, data) {
      commit('addToHistory', data);
    },
    updateDrawingData({ commit }, data) {
      commit('setDrawingData', data);
    },
  },
});

export default store;
