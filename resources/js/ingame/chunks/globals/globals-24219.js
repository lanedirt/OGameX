const IPI = {
  config: null,
  loadingIndicator: null,
  highlights: [],
  highlightSteps: [],
  highlightStepIndex: 0,
  ipiCall: (url, callback) => {
    IPI.loadingIndicator.show();
    $.get(url, {}, (data) => {
      IPI.loadingIndicator.hide();
      const result = JSON.parse(data);
      token = result.newAjaxToken ?? token;

      if (typeof result.success === "undefined" || result.success !== true) {
        if (typeof result.error !== "undefined") {
          fadeBox(result.error, true);
        }

        return;
      }

      callback(result);
    });
  },
  trackTask: (taskId) => {
    IPI.ipiCall(IPI.config.trackTaskUrl + "&token=" + (token ?? "0") + "&taskId=" + taskId, (result) => {
      const taskElements = document.querySelectorAll(".ipiTaskItem");
      taskElements.forEach((taskElement) => {
        const taskTrackElement = taskElement.querySelector(".ipiTaskItemTrack");
        const currentState = taskElement.getAttribute("data-state") ?? "none";

        if (parseInt(taskElement.getAttribute("data-taskid")) === taskId) {
          taskElement.setAttribute("data-state", currentState === "none" ? "tracked" : "none");
          taskTrackElement.textContent =
            currentState === "none" ? IPI.config.loca.LOCA_IPI_UNTRACK_TASK : IPI.config.loca.LOCA_IPI_TRACK_TASK;
          IPI.showTaskDescription(taskElement);
          return;
        }

        if (currentState === "tracked") {
          taskElement.setAttribute("data-state", "none");
          taskTrackElement.textContent = IPI.config.loca.LOCA_IPI_TRACK_TASK;
        }
      });

      if (typeof result.trackedAction !== "undefined") {
        const actionTitle = result.trackedAction?.title ?? "";
        const actionHighlights = result.trackedAction?.highlights ?? [];
        IPI.updateCurrentAction(actionTitle, actionHighlights);
      }
    });
  },
  collectTask: (taskId) => {
    IPI.ipiCall(IPI.config.collectTaskUrl + "&token=" + (token ?? "0") + "&taskId=" + taskId, (result) => {
      IPI.markTaskAsCollected(document.querySelector('.ipiTaskItem[data-taskid="' + taskId + '"]'));

      if (typeof result.unclaimedRewards !== "undefined") {
        const collectHint = document.querySelector("#ipiOverviewChapters .ipiChapterItem.active .ipiHintCollect");

        if (parseInt(result.unclaimedRewards) > 0) {
          collectHint.textContent = result.unclaimedRewards;
        } else {
          collectHint.remove();
        }
      }

      getAjaxResourcebox();
      fadeBox(result.claimedRewardsRendered ?? "", false);
    });
  },
  collectChapter: (chapterId) => {
    if (chapterId <= 0) {
      console.error("Invalid ChapterId: " + chapterId);
      return;
    }

    IPI.ipiCall(IPI.config.collectChapterUrl + "&token=" + (token ?? "0") + "&chapterId=" + chapterId, (result) => {
      const collectChapterElement = document.querySelector("#ipiOverviewChapterRewards .ipiOverviewCollectRewards");
      collectChapterElement.classList.add("disabled");
      collectChapterElement.textContent = IPI.config.loca.LOCA_IPI_CHAPTER_COLLECTED;
      document.querySelectorAll('.ipiTaskItem:not([data-state="collected"])').forEach(IPI.markTaskAsCollected);
      getAjaxResourcebox();
      fadeBox(result.claimedRewardsRendered ?? "", false);
    });
  },
  initIpiLayer: (config) => {
    IPI.config = config;
    IPI.loadingIndicator = $("#ipiOverviewContent").ogameLoadingIndicator();
    document.querySelectorAll(".ipiOverviewSelectChapter").forEach((linkElement) => {
      linkElement.addEventListener("click", (event) => IPI.loadingIndicator.show());
    });
    const taskItems = document.querySelectorAll(".ipiTaskItem");
    taskItems.forEach((taskItem) => {
      const taskId = parseInt(taskItem.getAttribute("data-taskid") ?? 0);
      taskItem.querySelector(".ipiTaskItemTitle")?.addEventListener("click", (event) => {
        if (taskItem.classList.contains("active")) {
          IPI.hideTaskDescription(taskItem);
          return;
        }

        IPI.showTaskDescription(taskItem);
      });

      if (taskItem.getAttribute("data-state") === "tracked") {
        IPI.showTaskDescription(taskItem);
      }

      if (taskId === 5001 && taskItem.getAttribute("data-state") !== "collected") {
        IPI.showTaskDescription(taskItem);
      }

      const trackElement = taskItem.querySelector(".ipiTaskItemTrack");
      trackElement.addEventListener("click", (event) => {
        if (IPI.config === null) {
          console.error("IPI Config missing");
          return;
        }

        const state = taskItem.getAttribute("data-state") ?? "none";

        if (taskId <= 0) {
          console.error("Invalid TaskId: " + taskId);
          return;
        }

        switch (state) {
          case "none":
          case "tracked":
            IPI.trackTask(taskId);
            break;

          case "completed":
            IPI.collectTask(taskId);
        }
      });
      const collectElement = taskItem.querySelector(".ipiOverviewCollectRewards");
      collectElement.addEventListener("click", (event) => {
        if (taskItem.getAttribute("data-state") !== "completed") {
          return;
        }

        IPI.collectTask(taskId);
      });
      const progressElement = taskItem.querySelector(".ipiTaskItemProgress");
      const progress = parseInt(progressElement.getAttribute("data-progress") ?? 0);
      const total = parseInt(progressElement.getAttribute("data-total") ?? 0);

      if (progress > 0 && total > 0 && total >= progress) {
        const progressPercent = (progress / total) * 100;
        let progressColor = "#5a5716";
        let backgroundColor = "transparent";

        if (progressPercent >= 100) {
          progressColor = "#4f4f4f";
        }

        progressElement.style.background = `linear-gradient(90deg, ${progressColor} ${progressPercent}%, ${backgroundColor} ${progressPercent}%)`;
      }

      const ipiMenuWrapper = document.querySelector("#ipiMenuWrapper");

      if (ipiMenuWrapper !== null) {
        changeTooltip(ipiMenuWrapper, "");
      }
    });
    const collectChapterElement = document.querySelector("#ipiOverviewChapterRewards .ipiOverviewCollectRewards");
    collectChapterElement.addEventListener("click", (event) => {
      if (IPI.config === null) {
        console.error("IPI Config missing");
        return;
      }

      if (collectChapterElement.classList.contains("disabled")) {
        return;
      }

      const target = parseInt(collectChapterElement.getAttribute("data-target") ?? 0);

      if (target <= 0) {
        console.error("Invalid ChapterId: " + target);
        return;
      }

      IPI.collectChapter(target);
    });
  },
  showTaskDescription: (taskItem) => {
    taskItem.classList.add("active");
    const contentElement = taskItem.querySelector(".ipiTaskItemContent");
    $(contentElement).slideDown();
  },
  hideTaskDescription: (taskItem) => {
    taskItem.classList.remove("active");
    const contentElement = taskItem.querySelector(".ipiTaskItemContent");
    $(contentElement).slideUp();
  },
  markTaskAsCollected: (taskElement) => {
    const taskTrackElement = taskElement.querySelector(".ipiTaskItemTrack");
    taskElement.setAttribute("data-state", "collected");
    taskTrackElement.textContent = IPI.config.loca.LOCA_IPI_TASK_COLLECTED;
    const claimTaskRewardsButton = taskElement.querySelector(".ipiTaskItemContentCollect .claimTaskRewards");
    claimTaskRewardsButton.textContent = IPI.config.loca.LOCA_IPI_TASK_COLLECTED;
    claimTaskRewardsButton.classList.add("disabled");
  },
  addHighlight: (highlightName) => {
    if (typeof highlightName === "object" && highlightName.length > 0) {
      IPI.highlightSteps = highlightName;
      IPI.highlightStepIndex = 0;
      return IPI.addHighlight(highlightName[IPI.highlightStepIndex]);
    }

    const targets = document.querySelectorAll('.ipiHintable[data-ipi-hint="' + highlightName + '"]');

    if (targets.length <= 0) {
      return false;
    }

    targets.forEach((target) => {
      target.classList.add("ipiHintActive");
    });
    return true;
  },
  nextHighlightStep: (event) => {
    if (IPI.highlightSteps.length <= 0 || IPI.highlightStepIndex >= IPI.highlightSteps.length) {
      return;
    }

    const step = event.target.getAttribute("data-ipi-highlight-step");
    const previousHighlights = document.querySelectorAll('.ipiHintActive[data-ipi-hint="' + step + '"]');

    if (previousHighlights.length <= 0) {
      return;
    }

    previousHighlights.forEach((target) => {
      target.classList.remove("ipiHintActive");
    });
    IPI.highlightStepIndex += 1;
    IPI.addHighlight(IPI.highlightSteps[IPI.highlightStepIndex]);
  },
  initIpiHighlights: (highlights) => {
    IPI.highlights = highlights;
    IPI.refreshHighlights();
    document.querySelectorAll("[data-ipi-highlight-step]").forEach((target) => {
      if (target.nodeName.toLowerCase() === "input") {
        target.addEventListener("change", IPI.nextHighlightStep);
        return;
      }

      target.addEventListener("click", IPI.nextHighlightStep);
    });
  },
  refreshHighlights: () => {
    document.querySelectorAll(".ipiHintable.ipiHintActive").forEach((target) => {
      target.classList.remove("ipiHintActive");
    }); //this approach is more friendly to old browsers, since they do not support Array.toReversed yet

    const tmpHighlights = Array.from(IPI.highlights).reverse();

    for (highlight of tmpHighlights) {
      if (typeof highlight === "object") {
        let highlightAdded = false;
        highlight.forEach((sub) => (highlightAdded = IPI.addHighlight(sub) || highlightAdded));

        if (highlightAdded === true) {
          break;
        }

        continue;
      }

      if (IPI.addHighlight(highlight) === true) {
        break;
      }
    }
  },
  updateCurrentAction: (title, highlights) => {
    const currentTaskComponent = document.querySelector("#ipimenucomponent .ipiMenuBody");
    const currentTaskFooter = document.querySelector("#ipimenucomponent .ipiMenuFooter");
    currentTaskComponent.textContent = title;

    if (title === "") {
      currentTaskComponent.classList.add("hidden");
      currentTaskFooter.classList.add("hidden");
    } else {
      currentTaskComponent.classList.remove("hidden");
      currentTaskFooter.classList.remove("hidden");
    }

    IPI.highlights = highlights;
    IPI.refreshHighlights();
  },
  initializeMenuContent: (ipiMenuConfig) => {
    $.ajax({
      url: ipiMenuConfig.ipiMenuContentUrl,
    })
      .done((response) => {
        IPI.populateMenuContent(response);
      })
      .fail(() => {
        fadeBox(result.error, true);
        return false;
      });
    $(".ipiMenuBody").mouseover();
    IPI.initIpiHighlights(ipiMenuConfig.highlights);
  },
  populateMenuContent: (content) => {
    $("#ipiMenuWrapper").html(content);
  },
  updateMenuContent: (content) => {
    if (typeof content.unclaimedRewards !== "undefined") {
      const collectHint = $("#ipimenucomponent .ipiHintCollect");

      if (parseInt(content.unclaimedRewards) > 0) {
        collectHint.textContent = content.unclaimedRewards;
      } else {
        collectHint.remove();
      }
    }

    if (typeof content.trackedAction !== "undefined") {
      const actionTitle = content.trackedAction?.title ?? "";
      const actionHighlights = content.trackedAction?.highlights ?? [];
      IPI.updateCurrentAction(actionTitle, actionHighlights);
    }
  },
};
