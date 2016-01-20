<?php

abstract class PhabricatorProjectUserListView extends AphrontView {

  private $project;
  private $userPHIDs;
  private $limit;

  public function setProject(PhabricatorProject $project) {
    $this->project = $project;
    return $this;
  }

  public function getProject() {
    return $this->project;
  }

  public function setUserPHIDs(array $user_phids) {
    $this->userPHIDs = $user_phids;
    return $this;
  }

  public function getUserPHIDs() {
    return $this->userPHIDs;
  }

  public function setLimit($limit) {
    $this->limit = $limit;
    return $this;
  }

  public function getLimit() {
    return $this->limit;
  }

  abstract protected function canEditList();
  abstract protected function getNoDataString();
  abstract protected function getRemoveURI($phid);
  abstract protected function getHeaderText();

  public function render() {
    $viewer = $this->getUser();
    $project = $this->getProject();
    $user_phids = $this->getUserPHIDs();

    $can_edit = $this->canEditList();
    $no_data = $this->getNoDataString();

    $list = id(new PHUIObjectItemListView())
      ->setNoDataString($no_data);

    $limit = $this->getLimit();

    // If we're showing everything, show oldest to newest. If we're showing
    // only a slice, show newest to oldest.
    if (!$limit) {
      $user_phids = array_reverse($user_phids);
    }

    $handles = $viewer->loadHandles($user_phids);

    // Always put the viewer first if they are on the list.
    $user_phids = array_fuse($user_phids);
    $user_phids =
      array_select_keys($user_phids, array($viewer->getPHID())) +
      $user_phids;

    if ($limit) {
      $render_phids = array_slice($user_phids, 0, $limit);
    } else {
      $render_phids = $user_phids;
    }

    foreach ($render_phids as $user_phid) {
      $handle = $handles[$user_phid];

      $item = id(new PHUIObjectItemView())
        ->setHeader($handle->getFullName())
        ->setHref($handle->getURI())
        ->setImageURI($handle->getImageURI());

      if ($can_edit) {
        $remove_uri = $this->getRemoveURI($user_phid);

        $item->addAction(
          id(new PHUIListItemView())
            ->setIcon('fa-times')
            ->setName(pht('Remove'))
            ->setHref($remove_uri)
            ->setWorkflow(true));
      }

      $list->addItem($item);
    }

    if ($user_phids) {
      $header = pht(
        '%s (%s)',
        $this->getHeaderText(),
        phutil_count($user_phids));
    } else {
      $header = $this->getHeaderText();
    }

    return id(new PHUIObjectBoxView())
      ->setHeaderText($header)
      ->setObjectList($list);
  }

}