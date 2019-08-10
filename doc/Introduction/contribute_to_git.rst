===================== 
Contribute to Git
=====================

Steps to contribute your changes / patches in open source repository.

Preparing your Fork
^^^^^^^^^^^^^^^^^^^

1. Hit 'fork' on Github, creating e.g. ``yourname/theproject``.
2. Clone your project: ``git clone git@github.com:yourname/theproject``.
3. Create a branch: ``cd theproject; git checkout -b foo-the-bars 3.5``.

Making your Changes
^^^^^^^^^^^^^^^^^^^

1. Add changelog entry crediting yourself.
2. Write tests expecting the correct/fixed functionality; make sure they fail.
3. Hack, hack, hack.
4. Run tests again, making sure they pass.
5. Commit your changes: ``git commit -m "Foo the bars"``

Creating Pull Requests
^^^^^^^^^^^^^^^^^^^^^^

1. Push your commit to get it back up to your fork: ``git push origin HEAD``
2. Visit Github, click handy "Pull request" button that it will make upon
   noticing your new branch.
3. In the description field, write down issue number (if submitting code fixing
   an existing issue) or describe the issue + your fix (if submitting a wholly
   new bugfix).
4. Hit 'submit'! And please be patient - the maintainers will get to you when
   they can.
